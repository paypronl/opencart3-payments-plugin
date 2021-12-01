<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProCreditcard extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'creditcard';
	}
}
