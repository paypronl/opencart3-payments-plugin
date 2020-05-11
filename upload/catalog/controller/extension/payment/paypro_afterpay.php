<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProAfterpay extends PayProPaymentCatalogController {

	public function getPaymentMethod() {
		return 'afterpay/giro';
	}
}
