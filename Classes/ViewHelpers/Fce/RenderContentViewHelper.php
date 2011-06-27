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
 * ViewHelper used to render the FlexForm definition for Fluid FCEs
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Fce/Field
 */
class Tx_Fed_ViewHelpers_Fce_RenderContentViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {


	public function initializeArguments() {
		$this->registerArgument('area', 'string', 'Name of the area to render');

	}

	public function render() {
		$html = "";
		$fce = $this->templateVariableContainer->get('FEDFCE');
		$detectedArea = $fce[0]['areas'][0]['name'];
		foreach ($fce as $group) {
			foreach ($group['areas'] as $area) {
				if ($area['name'] == $this->arguments['area']) {
					$detectedArea = $area;
				}
			}
		}
		$record = $this->templateVariableContainer->get('record');
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tt_content',
				"colPos = '255' AND tx_fed_fcecontentarea = '{$detectedArea['name']}:{$record['uid']}' AND deleted = 0 AND hidden = 0");
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$conf = array(
				'tables' => 'tt_content',
				'source' => $row['uid'],
				'dontCheckPid' => 1
			);
			$html .= $GLOBALS['TSFE']->cObj->RECORDS($conf);
		}
		return $html;
	}

}

?>