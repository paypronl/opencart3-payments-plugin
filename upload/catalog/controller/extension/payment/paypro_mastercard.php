<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProMastercard extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'creditcard/mastercard';
	}
}
