<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProVisa extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'creditcard/visa';
	}
}
