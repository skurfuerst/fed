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
 * Case for SwitchViewHelper
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers
 *
 */
class Tx_Fed_ViewHelpers_CaseViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('case', 'string', 'Value which triggers this case - reserved name "default" used for default case', TRUE);
		$this->registerArgument('break', 'boolean', 'If TRUE, breaks switch on encountering this case', FALSE, FALSE);
	}

	/**
	 * Renders the case and returns array of content and break-boolean
	 *
	 * @return array
	 */
	public function render() {
		$matchesCase = $this->viewHelperVariableContainer->get('Tx_Fed_ViewHelpers_SwitchViewHelper', 'switchCaseValue') == $this->arguments['case'];
		$mustContinue = $this->viewHelperVariableContainer->get('Tx_Fed_ViewHelpers_SwitchViewHelper', 'switchContinueUntilBreak');
		$isDefault = $this->arguments['case'] == 'default';
		if ($matchesCase || $mustContinue || $isDefault) {
			if ($this->arguments['break'] === TRUE) {
				$this->viewHelperVariableContainer->addOrUpdate('Tx_Fed_ViewHelpers_SwitchViewHelper', 'switchBreakRequested', TRUE);
			} else {
				$this->viewHelperVariableContainer->addOrUpdate('Tx_Fed_ViewHelpers_SwitchViewHelper', 'switchContinueUntilBreak', TRUE);
			}
			return $this->renderChildren();
		}
		return NULL;
	}

}

?>