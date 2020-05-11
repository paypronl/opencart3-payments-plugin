<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProPaypal extends PayProPaymentCatalogController {

	public function getPaymentMethod() {
		return 'paypal/direct';
	}
}
