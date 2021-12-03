<?php

require_once(__DIR__ . '/paypro.php');

class ControllerExtensionPaymentPayProSofortPhysical extends ControllerExtensionPaymentPayPro {

	public function getPaymentMethod() {
		return 'sofort/physical';
	}
}
