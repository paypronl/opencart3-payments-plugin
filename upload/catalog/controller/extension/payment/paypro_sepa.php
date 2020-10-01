<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProSepa extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'banktransfer/sepa';
	}
}
