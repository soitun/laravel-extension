<?php namespace LaravelPlus\Extension\Templates;

class BladeExtension {

	/**
	 * Compile Blade comments into valid PHP.
	 *
	 * @return \Closure
	 */
	public static function comment()
	{
		return function ($value) {
			$pattern = sprintf('/%s((.|\s)*?)%s/', '{#', '#}');

			return preg_replace($pattern, '<?php /*$1*/ ?>', $value);
		};
	}

	/**
	 * Compile Blade plain code into valid PHP.
	 *
	 * @return \Closure
	 */
	public static function plain()
	{
		return function ($value) {
			$pattern = sprintf('/%s((.|\s)*?)%s/', '{@', '@}');

			return preg_replace($pattern, '<?php $1; ?>', $value);
		};
	}

}