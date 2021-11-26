<?php

class PayProHelper
{
	public static function getMethodIDFromClass($class)
	{
		$className = get_class($class);
		preg_match_all('/((?:^|[A-Z])[a-z]+)/', substr($className, strrpos($className, 'PayPro') + 6), $matches);

		return strtolower(join('_', $matches[0]));
	}

	public static function getPath($methodID = null)
	{
		$suffix = $methodID ? '_' . $methodID : '';

		return 'extension/payment/paypro' . $suffix;
	}

	/**
	 * @param $methodID
	 *
	 * @return string
	 */
	public static function getModuleCode($methodID = null)
	{
		$suffix = $methodID ? '_' . $methodID : '';

        return 'payment_paypro' . $suffix;
	}

	public static function getTemplate($template)
	{
        return 'extension/payment/' . $template;
	}

	public static function getPaymentMethods()
	{
		return [
			[
				'id' => 'afterpay',
			],
			[
				'id' => 'bancontact',
			],
			[
				'id' => 'ideal',
			],
			[
				'id' => 'mastercard',
			],
			[
				'id' => 'paypal',
			],
			[
				'id' => 'sepa',
			],
			[
				'id' => 'sepa_once',
			],
			[
				'id' => 'sofort_digital',
			],
			[
				'id' => 'sofort_physical',
			],
			[
				'id' => 'visa',
			],
		];
	}

	public static function withLabels($t, $methods) {
		$withLabels = [];
		foreach ($methods as $method) {
			$method['label'] = $t->get('payment_method_' . $method['id']);
			$withLabels[] = $method;
		}

		return $withLabels;
	}

	public static function prefixSettingsArray($array, $prefix) {
		return array_combine(
			array_map(function($k) use ($prefix) { return $prefix . $k; }, array_keys($array)),
			$array
		);
	}

	public static function languageToLocale($language) {
		if (in_array($language, ['nl-nl', 'nl', 'dutch'])) {
			return 'NL';
		}

		return 'EN';
	}
}
