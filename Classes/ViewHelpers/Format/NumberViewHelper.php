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
 * Helps with formatting numbers
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\Format
 */
class Tx_Fed_ViewHelpers_Format_NumberViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {
	
	/**
	 * Render a select
	 * @param float $number The number to be formatted
	 * @param int $decimals The number of decimals to output
	 * @param string $dsep The character used as decimal separator
	 * @param string $tsep The character used as thousands separator
	 * @param string $unit The unit to use as suffix for the rendered number
	 * @return string
	 */
	public function render($number, $decimals=NULL, $dsep=NULL, $tsep=NULL, $unit=NULL) {
		$str = number_format($number, $decimals, $dsep, $tsep);
		if ($unit) {
			$str .= $unit;
		}
		return $str;
	}
}
	

?>