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
 * @subpackage ViewHelpers\Data
 */
class Tx_WildsideExtbase_ViewHelpers_Data_VarViewHelper extends Tx_WildsideExtbase_Core_ViewHelper_AbstractViewHelper {
	
	/**
	 * Get or set a variable
	 * @param string $name
	 * @param mixed $value
	 * @param string $type
	 */
	public function render($name, $value=NULL, $type=NULL) {
		if ($value === NULL) {
			$value = $this->renderChildren();
		}
		if (trim($value) === '') {
			// we are echoing a variable
			return $this->templateVariableContainer->get($name);
		} else {
			// we are setting a variable
			if ($type === NULL) {
				if (is_object($value)) {
					$type = 'object';
				} else if (is_string($value)) {
					$type = 'string';
				} else if (is_int($value)) {
					$type = 'integer';
				} else if (is_float($value)) {
					$type = 'float';
				} else if (is_array($value)) {
					$type = 'array';
				}
			}
			$value = $this->typeCast($value, $type);
			if ($this->templateVariableContainer->exists($name)) {
				$this->templateVariableContainer->remove($name);
			}
			$this->templateVariableContainer->add($name, $value);
		}
		return '';
	}
	
	/**
	 * Type-cast a value with type $type
	 * @param mixed $value
	 * @param string $type
	 */
	private function typeCast($value, $type) {
		switch ($type) {
			case 'integer':
				$value = intval($value);
				break;
			case 'float':
				$value = floatval($value);
				break;
			case 'object':
				$value = (object) $value;
				break;
			case 'array':
				// cheat a bit; assume CSV
				$value = (array) explode(',', $value);
				break;
			case 'string':
			default:
				$value = (string) $value;
		}
		return $value;
	}
}

?>