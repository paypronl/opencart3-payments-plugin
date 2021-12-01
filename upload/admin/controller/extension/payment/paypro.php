<?php

require_once(DIR_SYSTEM . 'library/paypro/helper.php');
require_once(DIR_SYSTEM . 'library/paypro/api.php');

class ControllerExtensionPaymentPayPro extends Controller
{
	protected $methodID;
	protected $path;
	protected $code;
	protected $settingsCode;
	protected $errors = [];
	protected $data = [];

	public function __construct($registry) {
		parent::__construct($registry);

		$this->methodID = PayProHelper::getMethodIDFromClass($this);
		$this->path = PayProHelper::getPath($this->methodID);
		$this->code = PayProHelper::getModuleCode($this->methodID);
		$this->settingsCode = PayProHelper::getModuleCode();
	}

	/**
	 * On install add the payment_hash column to
	 *
	 * @return void
	 */
	public function install () {
		if (!$this->db->query('SHOW COLUMNS FROM `' . DB_PREFIX . 'order` LIKE \'paypro_payment_hash\'')->num_rows) {
			$this->db->query('ALTER TABLE `' . DB_PREFIX . 'order` ADD `paypro_payment_hash` VARCHAR(255) DEFAULT NULL;');
		}

		$userId = $this->getUserId();

		// Install extension
		$paymentMethods = PayProHelper::getPaymentMethods();
		foreach ($paymentMethods as $method) {
			$this->getExtensionModel()->install('payment', 'paypro_' . $method['id']);

			// Add permissions
			$this->model_user_user_group->addPermission($userId, 'access', PayProHelper::getPath($method['id']));
			$this->model_user_user_group->addPermission($userId, 'modify', PayProHelper::getPath($method['id']));
		}
	}

	/**
	 * Uninstall extension
	 *
	 * @return void
	 */
	public function uninstall () {
		$paymentMethods = PayProHelper::getPaymentMethods();
		foreach ($paymentMethods as $method) {
			$this->getExtensionModel()->uninstall('payment', 'paypro_' . $method['id']);
		}
	}

	/**
	 * Render the payment method's settings page.
	 */
	public function index () {
		// Load essential models
		$this->load->language($this->path);
		$this->load->model('setting/setting');
		$this->load->model('setting/store');
		$this->load->model('localisation/geo_zone');
		$this->load->model('localisation/order_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$paymentMethods = PayProHelper::withLabels($this->language, PayProHelper::getPaymentMethods());
		$shops = $this->getMultiStores();
		$this->setMultiStoresData($shops);

		// Call validate method on POST
		if ($this->request->server['REQUEST_METHOD'] === 'POST') {
			$doRedirect = false;
			foreach($shops as $store) {
				if ($this->validate($store['id'])) {
					$settingsToSave = PayProHelper::prefixSettingsArray($this->request->post['stores'][$store['id']], $this->settingsCode . '_');
					$this->model_setting_setting->editSetting($this->settingsCode, $settingsToSave, $store['id']);

					$doRedirect = true;
				}
			}

			if ($doRedirect) {
				$this->session->data['success'] = $this->language->get('successfully_saved');
				$this->response->redirect(html_entity_decode($this->url->link($this->getExtensionsUri(), $this->getTokenParams(['type' => 'payment']), true)));
			}
		}

		// Set data for template
		$data['heading_title'] = $this->language->get('heading_title');
		$data['paypro_config_title'] = $this->language->get('paypro_config_title');
		$data['main_config_title'] = $this->language->get('main_config_title');
		$data['status_config_title'] = $this->language->get('status_config_title');
		$data['about_title'] = $this->language->get('about_title');

		$data['entry_test_mode'] = $this->language->get('test_mode');
		$data['entry_api_key'] = $this->language->get('api_key');
		$data['entry_product_id'] = $this->language->get('product_id');
		$data['entry_payment'] = $this->language->get('payment');
		$data['entry_geo_zone'] = $this->language->get('geo_zone');
		$data['entry_status'] = $this->language->get('status');
		$data['entry_sort_order'] = $this->language->get('sort_order');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		$data['failed_status'] = $this->language->get('failed_status');
		$data['canceled_status'] = $this->language->get('canceled_status');
		$data['pending_status'] = $this->language->get('pending_status');
		$data['expired_status'] = $this->language->get('expired_status');
		$data['completed_status'] = $this->language->get('completed_status');

		$data['payment_methods'] = $paymentMethods;

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['shops'] = $shops;

		// If there are errors, show the errors.
		$data['error_warning'] = isset($this->errors['warning']) ? $this->errors['warning'] : '';

		foreach($shops as $store) {
			if (isset($this->errors[$store['id']]['api_key'])) {
				$data['stores'][$store['id']]['error_api_key'] = $this->errors[$store['id']]['api_key'];
			} else {
				$data['stores'][$store['id']]['error_api_key'] = '';
			}
		}

		// Setup breadcrumbs
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'href'      => html_entity_decode($this->url->link('common/dashboard', $this->getTokenParams(), true)),
			'text'      => $this->language->get('text_home'),
			'separator' => false,
		];

		$data['breadcrumbs'][] = [
			'href'      => html_entity_decode($this->url->link($this->getExtensionsUri(), $this->getTokenParams(['type' => 'payment']), true)),
			'text'      => $this->language->get('payment'),
			'separator' => ' :: ',
		];

		$data['breadcrumbs'][] = [
			'href'     => html_entity_decode($this->url->link($this->path, $this->getTokenParams(), true)),
			'text'      => $this->language->get('heading_title'),
			'separator' => ' :: ',
		];

		// Form action url
		$data['action'] = html_entity_decode($this->url->link($this->path, $this->getTokenParams(), true));
		$data['cancel'] = html_entity_decode($this->url->link($this->getExtensionsUri(), $this->getTokenParams(['type' => 'payment']), true));

		// Default settings
		$settings = [
			'test_mode' => false,
			'api_key' => null,
			'product_id' => null,
			'pending_status_id' => 1,
			'failed_status_id' => 10,
			'canceled_status_id' => 7,
			'expired_status_id' => 14,
			'completed_status_id' => 2,
			'geo_zone_id' => null,
		];

		foreach($paymentMethods as $index => $method) {
			$settings[$method['id'] . '_status'] = false;
			$settings[$method['id'] . '_sort_order'] = $index + 1;
		}

		foreach($shops as $store) {
			$dbSettings = $this->model_setting_setting->getSetting($this->settingsCode, $store['id']);

			foreach ($settings as $key => $value) {
				if (isset($this->request->post['stores'][$store['id']][$key])) {
					// Get the value from the POST request
					$data['stores'][$store['id']][$key] = $this->request->post['stores'][$store['id']][$key];
				} else {
					// Get the value from the db or use the default value
					$dbKey = $this->settingsCode . '_' . $key;
					$data['stores'][$store['id']][$key] = isset($dbSettings[$dbKey]) ? $dbSettings[$dbKey] : $value;
				}
			}
		}

		$this->renderTemplate('paypro', $data, ['header', 'column_left', 'footer']);
	}

	/**
	 * Validate settings post
	 *
	 * @param int $store The store id
	 * @return bool
	 */
	private function validate ($store = 0) {
		if (!$this->user->hasPermission('modify', $this->path)) {
			$this->errors['warning'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->post['stores'][$store]['api_key'])
		    || !$this->request->post['stores'][$store]['api_key']) {
			$this->errors[$store]['api_key'] = $this->language->get('error_api_key');
		} else if (!isset($this->request->post['stores'][$store]['test_mode'])) {
			$this->errors[$store]['test_mode'] = $this->language->get('error_test_mode');
		}

		return (count($this->errors) == 0);
	}

	/**
	 * Map template handling for different Opencart versions
	 *
	 * @param string $template
	 * @param array $data
	 * @param array $common_children
	 *
	 * @return string
	 */
	protected function renderTemplate($template, $data, $common_children = []) {
		$template = PayProHelper::getTemplate($template);

		foreach ($common_children as $child) {
			$data[$child] = $this->load->controller('common/' . $child);
		}

		return $this->response->setOutput($this->load->view($template, $data));
	}

	/**
	 * Retrieve additional store id's from store table.
	 * Will not include default store. Only the additional stores. So we inject the default store here.
	 * @return array
	 */
	protected function getMultiStores()
	{
		$sql = $this->db->query(sprintf("SELECT store_id as id, name FROM %sstore", DB_PREFIX));
		$rows = $sql->rows;
		$default = array(
			array(
				'id' => 0,
				'name' => $this->config->get('config_name')
			)
		);
		$allStores = array_merge($default, $rows);

		return $allStores;
	}

	/**
	 * Set multi store data
	 *
	 * @param $shops
	 */
	protected function setMultiStoresData($shops) {
		foreach($shops as $store) {
			$sql = $this->db->query(sprintf("SELECT * FROM %ssetting WHERE store_id = %s", DB_PREFIX, $store['id']));
			$rows = $sql->rows;
			$newArrray = array();
			foreach($rows as $setting) {
				$newArrray[$setting['key']] = $setting['value'];
			}

			$this->data['stores'][$store['id']] = $newArrray;
		}
	}

	/**
	 * Get the extension model
	 *
	 * @return Model
	 */
	protected function getExtensionModel() {
		if (PayProHelper::isOpenCart3x()) {
			$this->load->model('setting/extension');
			return $this->model_setting_extension;
		}

		$this->load->model('extension/extension');
		return $this->model_extension_extension;
	}

	/**
	 * @return string
	 */
	private function getExtensionsUri() {
		if (PayProHelper::isOpenCart3x()) {
			return 'marketplace/extension';
		}

		if (PayProHelper::isOpenCart23x()) {
			return 'extension/extension';
		}

		return 'extension/payment';
	}

	/**
	 * @param array $additionalParams
	 *
	 * @return array|string
	 */
	private function getTokenParams($additionalParams = []) {
		$params = array_merge(
			PayProHelper::isOpenCart3x() ? [ 'user_token' => $this->session->data['user_token']] : [ 'token' => $this->session->data['token']],
			$additionalParams
		);

		if (PayProHelper::isOpenCart23x()) {
			return $params;
		}

		return '&' . http_build_query($params);
	}

	private function getUserId() {
		$this->load->model('user/user_group');

		if (method_exists($this->user, 'getGroupId')) {
			return $this->user->getGroupId();
		}

		return $this->user->getId();
	}
}
