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
 * @subpackage ViewHelpers\Be
 */
class Tx_Fed_ViewHelpers_Be_ContentAreaViewHelper extends Tx_Fed_Core_ViewHelper_AbstractBackendViewHelper {

	/**
	 * Render uri
	 *
	 * @return string
	 */
	public function render() {

		$row = $this->arguments['row'];
		$area = $this->arguments['area'];

		$pageRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', "uid = '{$row['pid']}'");
		$pageRecord = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pageRes);
		$dblist = t3lib_div::makeInstance('tx_cms_layout');
		$dblist->backPath = $GLOBALS['BACK_PATH'];
		$dblist->thumbs = $this->imagemode;
		$dblist->script = 'db_layout.php';
		$dblist->showIcon = 1;
		$dblist->setLMargin = 0;
		$dblist->doEdit = TRUE;
		$dblist->ext_CALC_PERMS = $GLOBALS['BE_USER']->calcPerms($pageRecord);
		$dblist->id = $row['pid'];
		$dblist->nextThree = 1;
		$dblist->showCommands = 1;

		$records = array();
		$condition = "tx_fed_fcecontentarea = '{$area}:{$row['uid']}' AND deleted = 0";
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_content', $condition, 'uid', 'sorting ASC');
		$records = $dblist->getResult($res);

		$this->templateVariableContainer->add('records', $records);
		$this->templateVariableContainer->add('dblist', $dblist);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('records');
		$this->templateVariableContainer->remove('dblist');

		return $content;
	}
}

?>