<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProSofortDigital extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'sofort/digital';
	}
}
