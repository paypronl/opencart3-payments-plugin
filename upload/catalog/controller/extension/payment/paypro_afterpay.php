<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProAfterpay extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'afterpay/giro';
	}
}
