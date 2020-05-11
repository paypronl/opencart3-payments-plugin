<?php

require_once(dirname(DIR_SYSTEM) . '/catalog/model/extension/payment/paypro/PayProPaymentCatalogController.php');

class ControllerExtensionPaymentPayProIdeal extends PayProPaymentCatalogController {
	public function getTemplateData() {
		$issuers = $this->payProApi->getIdealIssuers();

		return [
			'text_select_your_bank' => $this->language->get('text_select_your_bank'),
			'issuers' => $issuers,
		];
	}

	public function getPaymentMethod() {
		if (isset($this->request->get['issuer'])) {
			return $this->request->get['issuer'];
		}

		// This will throw an exception when trying to create the payment
		return '';
	}
}
