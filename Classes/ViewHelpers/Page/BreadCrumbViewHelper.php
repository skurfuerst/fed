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
 * ViewHelper to make a breadcrumb link set from a pageUid, automatic or manual
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Page
 */
class Tx_Fed_ViewHelpers_Page_BreadCrumbViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var t3lib_pageSelect
	 */
	protected $pageSelect;

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('pageUid', 'integer', 'Optional parent page UID to use as start of breadcrumbtrail/rootline - if left out, $GLOBALS[TSFE]->id is used', FALSE, NULL);
	}

	/**
	 * @return string
	 */
	public function render() {
		$this->pageSelect = new t3lib_pageSelect();
		$pageUid = $this->arguments['pageUid'];
		$entryLevel = $this->arguments['entryLevel'];
		$rootLine = $this->pageSelect->getRootLine($GLOBALS['TSFE']->id);
		$this->templateVariableContainer->add('rootLine', $rootLine);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('rootLine');
		if (strlen(trim($content)) === 0) {
			$content = $this->autoRender($rootLine);
		}
		return $content;
	}

	/**
	 * Use default rendering approach
	 *
	 * @param array $rootLine
	 * @return string
	 */
	protected function autoRender($rootLine) {

	}



}
?>