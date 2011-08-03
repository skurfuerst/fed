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
 * @subpackage Core/ViewHelper
 */
class Tx_Fed_Core_ViewHelper_AbstractFceViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Render method
	 */
	public function render() {
		$this->renderChildren();
		return '';
	}

	/**
	 * @param array $config
	 * @return void
	 */
	protected function addField($config) {
		$storage = $this->getStorage();
		array_push($storage['fields'], $config);
		$this->setStorage($storage);
	}

	/**
	 * @param array $config
	 * @return void
	 */
	protected function addContentArea($config) {
		$storage = $this->getStorage();
		$row = count($storage['grid']) - 1;
		$col = count($storage['grid'][$row]) - 1;
		array_push($storage['grid'][$row][$col]['areas'], $config);
		$this->setStorage($storage);
	}

	/**
	 * @return void
	 */
	protected function addGridRow() {
		$storage = $this->getStorage();
		array_push($storage['grid'], array());
		$this->setStorage($storage);
	}

	/**
	 * @param array $config
	 * @return void
	 */
	protected function addGridColumn($config) {
		$storage = $this->getStorage();
		$row = count($storage['grid']) - 1;
		array_push($storage['grid'][$row], $config);
		$this->setStorage($storage);
	}

	/**
	 * Get the internal FCE storage array
	 * @return array
	 */
	protected function getStorage() {
		return $this->viewHelperVariableContainer->get('Tx_Fed_ViewHelpers_FceViewHelper', 'storage');
	}

	/**
	 * Set the internal FCE storage array
	 * @param a $storage
	 * @return void
	 */
	protected function setStorage($storage) {
		$this->viewHelperVariableContainer->addOrUpdate('Tx_Fed_ViewHelpers_FceViewHelper', 'storage', $storage);
	}

}

?>
