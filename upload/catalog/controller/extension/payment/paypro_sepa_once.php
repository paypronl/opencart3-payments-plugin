<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProSepaOnce extends PayProPaymentCatalogController {

	public function getPaymentMethod() {
		return 'directdebit/sepa-once';
	}
}
