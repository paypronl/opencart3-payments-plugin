<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProBancontact extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'bancontact/mistercash';
	}
}
