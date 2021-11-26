<?php

require_once(__DIR__ . '/client/init.php');

class PayProApi {
	/**
	 * Paypro api responses
	 */
	public const PAYPRO_API_RES_APIKEY_INVALID = "API key not valid";
	public const PAYPRO_API_RES_NOT_SUBSCRIBED = "Not subscribed to money transfer service";

	protected $apiKey;
	protected $productID;
	protected $testMode;

	public function __construct($apiKey, $productID, $testMode) {
		$this->apiKey = $apiKey;
		$this->productID = $productID;
		$this->testMode = $testMode;
	}

	/**
	 * Create a new payment
	 *
	 * @param $data
	 *
	 * @return bool|mixed|null
	 */
	public function createPayment($data) {
		$client = new PayProClient($this->apiKey);
		$params = array_merge($data, ['test_mode' => $this->testMode]);

		if ($this->productID) {
			$client->setCommand('create_product_payment');
			$client->setParams(array_merge($params, ['product_id' => $this->productID]));
		} else {
			$client->setCommand('create_payment');
			$client->setParams($params);
		}
		try {
			$response = $client->execute();

			return $response['return'];
		} catch (Exception $exception) {
			return false;
		}
	}

	/**
	 * Get a payment by hash
	 *
	 * @param $paymentHash
	 *
	 * @return null
	 */
	public function getPayment($paymentHash) {
		$client = new PayProClient($this->apiKey);
		$client->setCommand('get_sale');
		$client->setParams([
			'payment_hash' => $paymentHash,
		]);

		try {
			$response = $client->execute();

			return $response['return'];
		} catch (Exception $exception) {
			return null;
		}
	}

	public function getIdealIssuers() {
		$client = new PayProClient($this->apiKey);
		$client->setCommand('get_all_pay_methods');

		try {
			$issuers = [];
			$response = $client->execute();

			if (isset($response['return']['data']['ideal']['methods'])) {
				$methods = $response['return']['data']['ideal']['methods'];
				foreach ($methods as $method) {
					$issuers[$method['id']] = $method['name'];
				}
			}

			return $issuers;
		} catch (Exception $exception) {
			return null;
		}
	}
}