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
 * Implodes array to CSV
 * Data-only assist; does not render content
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\Data
 */
class Tx_Fed_ViewHelpers_ImplodeViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {
	
	/**
	 * Implodes array into CSV string. Useful for example when giving 
	 * initial payload data to JS:
	 * <ws:inject.js>
	 * var payload = '<ws:implode array="{exampleArrayOfUidsOrObjectStorage}" />';
	 * </ws:inject.js>
	 * 
	 * Or when initializing values for AJAX calls into input fields:
	 * <f:form.hidden value="{ws:implode(array: exampleArrayOfUidsOrObjectStorage)}" />
	 * 
	 * If $array is an ObjectStorage, it will be traversed and its Uids 
	 * will be used for the list.
	 * 
	 * @param string $array The array to be imploded
	 * @param string $glue String glue
	 * @return array
	 */
	public function render($array, $glue=',') {
		if (is_object($array)) {
			$values = array();
			foreach ($array as $item) {
				$values[] = $item->getUid();
			}
			$array = $values;
		}
		$str = implode($glue, $array);
		return $str;
	}
}
	

?>