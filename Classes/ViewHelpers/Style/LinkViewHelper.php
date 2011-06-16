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
 * @subpackage ViewHelpers\Style
 */
class Tx_Fed_ViewHelpers_Style_LinkViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * Tag name - changes according to mode of parent element, which can be a style.switch
	 *
	 * @var string
	 */
	public $tagName = 'a';

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('mode', 'string', 'How to render this style switcher, select/div/checkbox/radio. Div renders raw links, others have special output', FALSE, 'div');
		$this->registerArgument('multiple', 'boolean', 'If TRUE, multiple selections are allowed. If FALSE, only one selection. For checkbox/select/radio this means selection changes and stylesheets unload - for div mode this means the other stylesheets in the swicther are unloaded, "active" class removed and the current stylesheet loaded', FALSE, FALSE);

	}

	/**
	 * Renders/registers a stylesheet link - if a parent stylesheet handle is used, it registers itself with that parent
	 *
	 * @return string
	 */
	public function render() {
		return $rendered;
	}
}

?>