<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProSepaOnce extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'directdebit/sepa-once';
	}
}
