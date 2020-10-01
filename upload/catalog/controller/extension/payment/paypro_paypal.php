<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProPaypal extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'paypal/direct';
	}
}
