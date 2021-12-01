<?php

class PayProHelper
{
	public static function getMethodIDFromClass($class)
	{
		$className = get_class($class);
		preg_match_all('/((?:^|[A-Z])[a-z]+)/', substr($className, strrpos($className, 'PayPro') + 6), $matches);

		return strtolower(join('_', $matches[0]));
	}

	/**
	 * @return bool
	 */
	public static function isOpenCart3x()
	{
		return version_compare(VERSION, '3.0.0', '>=');
	}

	/**
	 * @return bool
	 */
	public static function isOpenCart23x()
	{
		return version_compare(VERSION, '2.3.0', '>=');
	}

	/**
	 * @return bool
	 */
	public static function isOpenCart22x()
	{
		return version_compare(VERSION, '2.2.0', '>=');
	}

	public static function getPath($methodID = null)
	{
		$suffix = $methodID ? '_' . $methodID : '';

		return PayProHelper::isOpenCart23x() ? 'extension/payment/paypro' . $suffix : 'payment/paypro' . $suffix;
	}

	/**
	 * @param $methodID
	 *
	 * @return string
	 */
	public static function getModuleCode($methodID = null)
	{
		$suffix = $methodID ? '_' . $methodID : '';

		if (self::isOpenCart3x()) {
			return 'payment_paypro' . $suffix;
		}

		return 'paypro' . $suffix;
	}

	public static function getTemplate($template)
	{
		$template = PayProHelper::isOpenCart23x() ? 'extension/payment/' . $template : 'payment/' . $template;

		if (!PayProHelper::isOpenCart3x()) {
			$template .= '.tpl';
		}

		return $template;
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
				'id' => 'creditcard',
			],
			[
				'id' => 'ideal',
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
				'id' => 'sofort',
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
