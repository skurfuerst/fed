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
 * ViewHelper used to render content elements in Fluid page templates
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Page/Field
 */
class Tx_Fed_ViewHelpers_Page_RenderContentViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {


	public function initializeArguments() {
		$this->registerArgument('column', 'integer', 'Name of the column to render', FALSE, 0);
		$this->registerArgument('as', 'string', 'If specified, adds template variable and assumes you manually iterate through {content}');
		$this->registerArgument('limit', 'integer', 'Optional limit to the number of content elements to render');
		$this->registerArgument('order', 'string', 'Optional sort field of content elements - RAND() supported', FALSE, 'sorting');
		$this->registerArgument('sortDirection', 'string', 'Optional sort direction of content elements', FALSE, 'ASC');
	}

	public function render() {
		if (TYPO3_MODE == 'BE') {
			return '';
		}
		$content = $this->getContentRecords();
		if ($this->arguments['as']) {
			$this->templateVariableContainer->add('content', $content);
			$html = $this->renderChildren();
			$this->templateVariableContainer->remove('content');
		} else {
			$html = "";
			foreach ($content as $contentRecord) {
				$html .= $contentRecord . chr(10);
			}
		}
		return $html;
	}

	protected function getContentRecords() {
		$pid = $GLOBALS['TSFE']->id;
		$order = $this->arguments['order'] . ' ' . $this->arguments['sortDirection'];
		$colPos = $this->arguments['column'];
		$conditions = "pid = '{$pid}' AND colPos = '{$colPos}' AND deleted = 0 AND hidden = 0";
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tt_content', $conditions, 'uid', $order, $this->arguments['limit']);
		$content = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$conf = array(
				'tables' => 'tt_content',
				'source' => $row['uid'],
				'dontCheckPid' => 0
			);
			array_push($content, $GLOBALS['TSFE']->cObj->RECORDS($conf));
		}
		return $content;
	}

}

?>