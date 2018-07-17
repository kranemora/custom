<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Custom\Utility;

/**
 * Text handling methods.
 */
class Sanitizer
{
	/**
	 * Trim recursively
     * based on trimDeep https://github.com/dereuromark/cakephp-tools/blob/master/src/Utility/Utility.php
	 *
	 * @param string|array|null $value
	 * @param bool $transformNullToString
	 * @return array|string
	 */
	public static function clean($value, $transformNullToString = true) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = static::clean($v, $transformNullToString);
			}
			return $value;
		}
		return ($value === null && !$transformNullToString) ? $value : trim(preg_replace('/\s+/', ' ', $value));
	}

}
