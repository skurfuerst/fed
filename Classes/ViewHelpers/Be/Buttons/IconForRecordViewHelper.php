<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Georg Ringer <typo3@ringerge.org>
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
 * ViewHelper to show sprite icon of a record
 *
 * = Examples=
 *
 * <code title="Default">
 * <fed:be.buttons.iconForRecord table="tx_news_domain_model_news" uid="[newsItem.uid}" title="[newsItem.title}" />
 * </code>
 * <output>
 * The correct icon for the record including every overlay is shown.
 * </output>
 *
 * @package TYPO3
 * @subpackage tx_fed
 */
class Tx_Fed_ViewHelpers_Be_Buttons_IconForRecordViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {

	/**
	 * Render the sprite icon
	 *
	 * @param string $table table name
	 * @param integer $uid uid of record
	 * @param string $title title
	 * @return string sprite icon
	 */
	public function render($table, $uid, $title) {
		$row = t3lib_BEfunc::getRecord($table, $uid);
		$icon = t3lib_iconWorks::getSpriteIconForRecord($table, $row, array('title' => htmlspecialchars($title)));

		return $icon;
	}
}

?>