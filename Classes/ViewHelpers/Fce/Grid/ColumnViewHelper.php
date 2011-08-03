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
 * @subpackage ViewHelpers/Fce/Grid
 */
class Tx_Fed_ViewHelpers_Fce_Grid_ColumnViewHelper extends Tx_Fed_Core_ViewHelper_AbstractFceViewHelper {

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('colspan', 'integer', 'Column span');
		$this->registerArgument('rowspan', 'integer', 'Column span');
		$this->registerArgument('width', 'string', 'Width of column, fx "50%" or "500px"', FALSE, 'auto');
		$this->registerArgument('repeat', 'integer', 'number of times to repeat this colum while appending $iteration to name', FALSE, 1);
	}

	/**
	 * @return array
	 */
	public function render() {
		for ($i=0; $i<=$this->arguments['repeat']; $i++) {
			$column = array(
				'colspan' => $this->arguments['colspan'],
				'rowspan' => $this->arguments['rowspan'],
				'width' => $this->arguments['width'],
				'repeat' => $this->arguments['repeat'],
				'areas' => array()
			);
			$this->addGridColumn($column);
			$this->templateVariableContainer->add('cycle', $i+1);
			$this->renderChildren();
			$this->templateVariableContainer->remove('cycle');
		}
		return '';
	}

}

?>