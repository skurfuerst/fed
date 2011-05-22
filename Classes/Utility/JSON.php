<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_JSON implements t3lib_Singleton {


	/**
	 * Detect the PHP version being used
	 *
	 * @return float
	 */
	private function getPHPVersion() {
		$segments = explode('.', phpversion());
		$major = array_shift($segments);
		$minor = array_shift($segments);
		$num = "{$major}.{$minor}";
		$num = (float) $num;
		return $num;
	}

	/**
	 * Get encoding options depending on PHP version
	 *
	 * @return int
	 */
	private function getEncodeOptions() {
		if ($this->getPHPVersion() >= 5.3) {
			return JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP;
		} else {
			return 0;
		}
	}


	/**
	 * Encode to working JSON depending on PHP version
	 *
	 * @param mixed $source
	 * @param int $options
	 */
	public function encode($source) {
		$options = $this->getEncodeOptions();
		$str = json_encode($source, $options);
		return $str;
	}


	/**
	 * Decode to working JSON depending on PHP version
	 *
	 * @param string $str
	 */
	public function decode($str) {
		$decoded = json_decode($str);
		return $decoded;
	}


}

?>