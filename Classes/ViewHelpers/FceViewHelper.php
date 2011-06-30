<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ************************************************************* */

/**
 *
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Fce
 */
class Tx_Fed_ViewHelpers_FceViewHelper extends Tx_Fed_Core_ViewHelper_AbstractFceViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('id', 'string', 'Identifier of this Flexible Content Element', TRUE);
		$this->registerArgument('label', 'string', 'Label for this Fluid FCE in template file selector', FALSE, NULL);
	}

	/**
	 * Render method
	 */
	public function render() {
		$storage = array();
		$this->templateVariableContainer->add('FEDFCEID', $this->arguments['id']);
		if ($this->arguments['label']) {
			$this->templateVariableContainer->add('FEDFCELABEL', $this->arguments['label']);
		} else {
			$this->templateVariableContainer->add('FEDFCELABEL', $this->arguments['id']);
		}
		$this->setStorage($storage);
		$this->renderChildren();
	}

}

?>
