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
 * @subpackage ViewHelpers\Extbase
 */
class Tx_Fed_ViewHelpers_Extbase_SpriteViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {
	
	/**
	 * Render the Field
	 * 
	 * @param string $displayType If specified, overrides JS instance type
	 * @param string $name Label, if any, to assign in this.config
	 * @param string $value Value, if any, to assign in this.config
	 */
	public function render($displayType='dk.wildside.display.Sprite', $name=NULL, $value=NULL) {
		$children = $this->renderChildren();
		$json = new stdClass();
		$json->displayType = $displayType;
		$json->name = $name;
		$json->value = $value;
		
		$jsonString = json_encode($json);
		
		$html = "<div class='fed-sprite'>";
		$html .= "<div class='fed-json'>{$jsonString}</div>";
		$html .= $children;
		$html .= "</div>";
		return $html;
	}
	
}

?>