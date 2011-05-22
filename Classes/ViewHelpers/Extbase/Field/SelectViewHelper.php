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
class Tx_Fed_ViewHelpers_Extbase_Field_SelectViewHelper extends Tx_Fed_ViewHelpers_Extbase_FieldViewHelper {

	/**
	 * Render the Field
	 *
	 * @param string $displayType Type (FED JS domain style) of Field
	 * @param array $options If multiple options, specify them here as value => label array
	 * @param string $selectedValue The pre-selected value among $options
	 * @param boolean $multi Whether or not this element allows multiple selections
	 * @param integer $size Size (height) of the selector. If null or 1, it will be a regular dropdown box.
	 */
	public function render(
			$displayType='dk.wildside.display.field.Select',
			array $options=array('No', 'Yes'),
			$selectedValue=NULL,
			$multi=FALSE,
			$size=NULL) {
		$name = $this->getFieldName();
		$value = $this->getFieldValue();
		$class = $this->getFieldClass();
		$html = "<select name='{$name}' class='{$class}'" . ($size && $size > 1 ? " size='{$size}'" : "") . ($multi ? " multiple='true'" : "") . ">";
		foreach ($options as $value=>$label) {
			if (is_object($label)) {
				$value = $label->getUid();
				$label = $label->getTitle();
			}
			if ($value == $selectedValue) {
				$selected = " selected='selected'";
			} else {
				$selected = '';
			}
			$field = "<option value='{$value}' {$selected}>{$label}</option>";
			$html .= $field;
		}
		$html .= "</select>";
		return $this->renderChildren($html);
	}

}


?>