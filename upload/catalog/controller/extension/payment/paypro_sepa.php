<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProSepa extends PayProPaymentCatalogController {

	public function getPaymentMethod() {
		return 'banktransfer/sepa';
	}
}
