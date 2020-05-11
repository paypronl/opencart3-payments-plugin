<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProSofortDigital extends PayProPaymentCatalogController {

	public function getPaymentMethod() {
		return 'sofort/digital';
	}
}
