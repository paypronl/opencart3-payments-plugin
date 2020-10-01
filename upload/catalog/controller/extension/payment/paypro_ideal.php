<?php

require_once(__DIR__ . '/paypro.php');
require_once(DIR_SYSTEM . 'library/paypro/api.php');

class ControllerExtensionPaymentPayProIdeal extends ControllerExtensionPaymentPayPro {
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
