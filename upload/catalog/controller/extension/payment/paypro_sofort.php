<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProSofort extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'sofort/physical';
	}
}
