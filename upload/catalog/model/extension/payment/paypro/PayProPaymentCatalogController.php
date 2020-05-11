<?php

require_once(__DIR__ . '/PayProHelper.php');
require_once(__DIR__ . '/PayProApi.php');

abstract class PayProPaymentCatalogController extends Controller {

	protected $methodID;
	protected $path;
	protected $settingsCode;
	protected $payProApi;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->methodID = PayProHelper::getMethodIDFromClass($this);
		$this->path = PayProHelper::getPath($this->methodID);
		$this->settingsCode = PayProHelper::getModuleCode();

		$apiKey = $this->config->get($this->settingsCode . '_api_key');
		$productID = $this->config->get($this->settingsCode . '_product_id');
		$testMode = $this->config->get($this->settingsCode . '_test_mode') === '1';
		$this->payProApi = new PayProApi($apiKey, $productID, $testMode);
	}

	/**
	 * Should return the confirmation button
	 *
	 * @return string
	 */
	public function index() {
		$this->load->language(PayProHelper::getPath());

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['redirect_route'] = $this->path . '/start_payment';

		$prefix = PayProHelper::isOpenCart22x() ? '' : 'default/template/';
		return $this->load->view( $prefix . PayProHelper::getTemplate('paypro'), array_merge($data, $this->getTemplateData()));
	}

	/**
	 * Start the payment and return the redirect uri
	 */
	public function start_payment() {
		$this->load->language(PayProHelper::getPath());

		$orderId = $this->session->data['order_id'];
		$json = [];

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->customer->getEmail()));

		if ($this->session->data['payment_method']['code'] === 'paypro_' . $this->methodID) {
			$this->load->model('checkout/order');

			$order = $this->model_checkout_order->getOrder($orderId);
			$amount = $this->currency->convert($order['total'], $this->config->get('config_currency'), 'EUR');
			$redirectUrl = html_entity_decode($this->url->link($this->path . '/payment_redirect', '&order_id=' . $orderId, true));

			$paymentData = [
				'amount' => round($amount, 2) * 100,
				'pay_method' => $this->getPaymentMethod(),
				'return_url' => $redirectUrl,
				'cancel_url' => $redirectUrl,
				'postback_url' => html_entity_decode($this->url->link($this->path . '/payment_callback', '', true)),
				'description' => $orderId . ' - ' . $this->config->get('config_name'),
				'locale' => PayProHelper::languageToLocale($order['language_code']),
				'custom' => strval($orderId),
				'consumer_email' => $order['email'],
				'consumer_firstname' => $order['payment_firstname'],
				'consumer_name' => $order['payment_lastname'],
				'consumer_phone' => $order['telephone'],
				'consumer_address' => trim($order['payment_address_1'] . ' ' . $order['payment_address_2']),
				'consumer_city' => $order['payment_city'],
				'consumer_companyname' => $order['payment_company'],
				'consumer_country' => $order['payment_country'],
				'consumer_postal' => $order['payment_postcode'],
			];

			$response = $this->payProApi->createPayment($paymentData);

			if ($response && isset($response['payment_hash'])) {
				$this->model_checkout_order->addOrderHistory($orderId, $this->config->get($this->settingsCode . '_pending_status_id'));

				// Add the payment hash to the order
				$this->db->query('UPDATE `' . DB_PREFIX . 'order` SET `paypro_payment_hash` = "'
				                 . $response['payment_hash'] . '" WHERE `order_id` = "' . $orderId . '"');

				$json['redirect'] = $response['payment_url'];
			} else {
				$json['error'] = $this->language->get('something_went_wrong');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Handle the user redirect
	 */
	public function payment_redirect() {
		$this->load->model('checkout/order');
		$this->load->language(PayProHelper::getPath());

		$orderId = $this->request->get['order_id'];
		$order = $this->model_checkout_order->getOrder($orderId);

		if ($order) {
			// Get the payment hash for the order
			$results = $this->db->query('SELECT `paypro_payment_hash` FROM `' . DB_PREFIX . 'order` WHERE `order_id` = "' . $orderId . '"');
			if ($results->num_rows) {
				// Get the payment
				$payment = $this->payProApi->getPayment($results->row['paypro_payment_hash']);

				if ($payment) {
					if ($payment['current_status'] === 'completed' || $payment['current_status'] === 'open') {
						$this->response->redirect(html_entity_decode($this->url->link('checkout/success', '', true)));
						return;
					} else if ($payment['current_status'] === 'canceled') {
						// Payment canceled
						$this->session->data['error'] = $this->language->get('response_canceled');
					} else {
						// Something went wrong
						$this->session->data['error'] = $this->language->get('something_went_wrong');
					}
				}
			}
		}

		$this->response->redirect(html_entity_decode($this->url->link('checkout/cart', '', true)));
	}

	/**
	 * Handle the callback
	 */
	public function payment_callback() {
		$this->load->model('checkout/order');
		$this->load->language(PayProHelper::getPath());

		$this->response->addHeader('Content-Type: application/json');
		$paymentHash = $this->request->post['payment_hash'];

		if ($paymentHash) {
			// Get the payment
			$payment = $this->payProApi->getPayment($paymentHash);

			if ($payment) {
				$orderId = intval($payment['custom']);
				$order = $this->model_checkout_order->getOrder($orderId);

				if ($order && $order['order_status_id'] === $this->config->get($this->settingsCode . '_pending_status_id')
				    && !in_array($payment['current_status'], ['open', 'pending'])) {
					$notify = false;

					switch ($payment['current_status']) {
						case 'canceled';
							$statusId = $this->config->get($this->settingsCode . '_canceled_status_id');
							$comment = $this->language->get('response_canceled');
							break;
						case 'expired';
							$statusId = $this->config->get($this->settingsCode . '_expired_status_id');
							$comment = $this->language->get('response_expired');
							break;
						case 'completed';
							$statusId = $this->config->get($this->settingsCode . '_completed_status_id');
							$comment = $this->language->get('response_completed');
							$notify = true;
							break;
						default;
							$statusId = $this->config->get($this->settingsCode . '_failed_status_id');
							$comment = $this->language->get('response_failed');
					}

					if ($statusId) {
						$this->model_checkout_order->addOrderHistory($orderId, intval($statusId), $comment, $notify);
						$this->response->setOutput(json_encode(['success' => true]));

						return;
					}
				}
			}
		}

		$this->response->setOutput(json_encode(['success' => false]));
	}

	public function getTemplateData() {
		return [];
	}

	public abstract function getPaymentMethod();
}
