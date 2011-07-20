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
class Tx_Fed_ViewHelpers_Be_ContentElementViewHelper extends Tx_Fed_Core_ViewHelper_AbstractBackendViewHelper {

	/**
	 * @param mixed $dblist
	 * @return string
	 */
	public function render($dblist) {
		$record = $this->arguments['row'];
		#$uid = $record['uid'];
		#$dblist = t3lib_div::makeInstance('tx_cms_layout');
		#$dblist->backPath = $GLOBALS['BACK_PATH'];
		#$dblist->thumbs = $this->imagemode;
		#$dblist->script = 'db_layout.php';
		#$dblist->showIcon = 1;
		#$dblist->showInfo = 1;
		#$dblist->setLMargin = 0;
		#$dblist->doEdit = TRUE;
		#$dblist->tt_contentData['nextThree'][$record['uid']] = $record['nextThree'];
		#$dblist->ext_CALC_PERMS = $GLOBALS['BE_USER']->calcPerms($pageRecord);
		#$dblist->id = $row['pid'];
		#$dblist->nextThree = 0;
		#$dblist->tt_contentData['next'][$uid] = $record['next'];
		#$dblist->tt_contentData['prev'][$uid] = $record['prev'];
		$rendered = $dblist->tt_content_drawHeader($record);
		$rendered .= $dblist->tt_content_drawItem($record);
		return $rendered;
	}

}
?>