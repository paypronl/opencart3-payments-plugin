<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProVisa extends PayProPaymentCatalogController {

	public function getPaymentMethod() {
		return 'creditcard/visa';
	}
}
