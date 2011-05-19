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
 * @subpackage ViewHelpers\Extbase\Field
 */
class Tx_Fed_ViewHelpers_Extbase_Field_RadioViewHelper extends Tx_Fed_ViewHelpers_Extbase_FieldViewHelper {
	
	/**
	 * Render the Field
	 * 
	 * @param string $displayType Type (FED JS domain style) of Field 
	 * @param string $name Name property of the Field
	 * @param string $value Value property of the Field
	 * @param string $class Class property of the Field
	 * @param mixed $options If multiple options, specify them here as value => label array
	 * @param string $selectedValue The pre-selected value among $options
	 * @param boolean $labels If FALSE, no labels are echoed
	 * @param string $sanitizer FED JS Domain style reference to validator method
	 */
	public function render(
			$displayType='dk.wildside.display.field.Radio', 
			$name=NULL, 
			$value=NULL, 
			$class=NULL, 
			$options=array('No', 'Yes'), 
			$selectedValue=NULL,
			$labels=TRUE,
			$sanitizer=NULL) {
		if (is_string($options)) {
			$options = explode(',', $options);
			foreach ($options as $k=>$v) {
				$options->$k = trim($v);
			}
		}
		$html = "<span class='input-radio-wrap'>";
		$uniqID = uniqid();
		foreach ($options as $value=>$label) {
			if ($value == $selectedValue) {
				$checked = " checked='checked'";
			} else {
				$checked = '';
			}
			
			if ($labels === FALSE) {
				$label = '';
			}
			
			$field = "<label class='input-field-label'><span class='input-field-label-text'>{$label}</span> <input type='radio' name='{$name}_{$uniqID}' class='input-radio {$class}' onclick='this.blur();' value='{$value}' {$checked} /></label>";
			$html .= $field;
		}
		$html .= "</span>";
		return parent::render($html, $displayType, $name, $value, NULL, $sanitizer);
	}
	
}


?>