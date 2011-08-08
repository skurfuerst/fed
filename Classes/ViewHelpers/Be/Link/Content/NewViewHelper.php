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
 * @subpackage ViewHelpers\Be\Uri\Content
 */
class Tx_Fed_ViewHelpers_Be_Link_Content_NewViewHelper extends Tx_Fed_Core_ViewHelper_AbstractBackendViewHelper {

	/**
	 * Render uri
	 *
	 * @return string
	 */
	public function render() {
		$pid = $this->arguments['row']['pid'];
		$uid = $this->arguments['row']['uid'];
		$area = $this->arguments['area'];
		$sysLang = $this->arguments['row']['sys_language_uid'];
		$colPos = $this->arguments['row']['colPos'];
		$returnUri = $this->getReturnUri($pid);
		if ($area) {
			$returnUri .= '%23' . $area;
		}
		$sign = $after ? '-' : '';
		$icon = $this->getIcon('actions-document-new', 'Insert new content element in this position');
		$uri = 'db_new_content_el.php?id=' . $pid
			. '&defVals[someVar]=test'
			. '&returnUrl=' . $returnUri
			. '&uid_pid=' . $sign . $uid
			. '&colPos=' . $colPos
			. '&sys_language_uid=0'
			;
		return $this->wrapLink($icon, $uri);
	}
}

?>