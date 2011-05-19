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
class Tx_Fed_ViewHelpers_Extbase_Field_ButtonViewHelper extends Tx_Fed_ViewHelpers_FieldViewHelper {
	
	/**
	 * Render the Field
	 * 
	 * @param string $displayType Type (FED JS domain style) of Field 
	 * @param string $name Name property of the Field
	 * @param string $value Value property of the Field
	 * @param string $class Class property of the Field
	 * @param string $type The type (button, reset, submit) of the <button> tag created
	 * @param string $label The text on the button itself
	 * @param string $sanitizer WS JS Domain style reference to validator method
	 */
	public function render($displayType='dk.wildside.display.field.Button', $name=NULL, $value=NULL, $class=NULL, $type='button', $label=NULL, $sanitizer=NULL) {
		if ($label === NULL) {
			$label = $this->renderChildren();
		}
		if (trim($label) == '') {
			$label = 'button';
		}
		$field = "<button type='{$type}' name='{$name}' class='{$class}'>{$label}</button>";
		return parent::render($field, $displayType, $name, $value, NULL, $sanitizer);
	}
	
}


?>