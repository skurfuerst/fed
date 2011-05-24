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
class Tx_Fed_ViewHelpers_Extbase_Field_MessageViewHelper extends Tx_Fed_ViewHelpers_Extbase_FieldViewHelper {

	/**
	 * Render the Field
	 *
	 * @param string $displayType Type (FED JS domain style) of Field
	 * @param string $tag The tagName to use as wrapper
	 * @param boolean $hidden If TRUE, start the Field as hidden (is hidden by Field constructor in JS)
	 * @param int $timeout Number of seconds this message stays visible. Zero for permanent visibility (in case you close the message in another way)
	 */
	public function render($displayType='dk.wildside.display.field.Message', $tag='div', $hidden=FALSE, $timeout=10) {
		$value = $this->getFieldValue();
		$name = $this->getFieldName();
		$class = $this->getFieldClass();
		if ($value === NULL) {
			$value = $this->renderChildren();
		}
		$displayTypeFixed = str_replace(".", "-", $displayType);
		$field = "<{$tag} class='{$class}'>{$value}</{$tag}>";
		$config = new stdClass();
		$config->hidden = $hidden ? 1 : 0;
		$config->timeout = $timeout;
		return $this->renderChildren($value);
	}

}


?>