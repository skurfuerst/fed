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
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\Debug
 *
 */
class Tx_WildsideExtbase_ViewHelpers_Debug_VariablesViewHelper extends Tx_WildsideExtbase_Core_ViewHelper_AbstractViewHelper {
	
	
	/**
	 * Dumps registered template variables
	 * 
	 * @param int $crop Maximum number of chars allowed in strings
	 * @return string
	 */
	public function render($crop=100) {
		$vars = $this->templateVariableContainer->getAll();
		$vars = $this->trim($vars);
		#$export = "<pre>" . var_export($vars, TRUE) . "</pre>";
		$export = t3lib_div::view_array($vars);
		return $export;
	}
	
	private function trim($vars) {
		if (is_array($vars) || is_object($vars)) {
			foreach ($vars as $k=>$v) {
				if ($v instanceof Tx_Extbase_DomainObject_AbstractDomainObject) {
					$vars[$k] = (string) $v;
				} else if (is_array($v) || $v instanceof ArrayAccess) {
					$vars[$k] = $this->trim($v);
				}
			}
		} else if (is_string($vars) && strlen($vars) > 100) {
			
		}
		return $vars;
	}
}

?>