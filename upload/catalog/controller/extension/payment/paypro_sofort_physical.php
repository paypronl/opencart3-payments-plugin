<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProSofortPhysical extends PayProPaymentCatalogController {

	public function getPaymentMethod() {
		return 'sofort/physical';
	}
}
