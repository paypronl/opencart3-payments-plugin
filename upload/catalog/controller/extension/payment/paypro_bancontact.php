<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProBancontact extends PayProPaymentCatalogController {

	public function getPaymentMethod() {
		return 'bancontact/mistercash';
	}
}
