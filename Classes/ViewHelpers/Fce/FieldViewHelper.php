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
 * @subpackage ViewHelpers/Fce
 */
abstract class Tx_Fed_ViewHelpers_Fce_FieldViewHelper extends Tx_Fed_Core_ViewHelper_AbstractFceViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Name of the attribute, FlexForm XML-valid tag name string', TRUE);
		$this->registerArgument('label', 'string', 'Label for the attribute, can be LLL: value', TRUE);
		$this->registerArgument('default', 'string', 'Default value for this attribute');
		$this->registerArgument('required', 'boolean', 'If TRUE, this attribute must be filled when editing the FCE', FALSE, FALSE);
		$this->registerArgument('exclude', 'boolean', 'If TRUE, this field becomes an "exclude field" (see TYPO3 documentation about this)', FALSE, FALSE);
		$this->registerArgument('wizards', 'array', 'FlexForm-style Wizard configuration array', FALSE, array());
	}

	/**
	 * Get a base configuration containing all shared arguments and their values
	 *
	 * @return array
	 */
	protected function getBaseConfig() {
		return array(
			'name' => $this->arguments['name'],
			'label' => $this->arguments['label'],
			'type' => $this->arguments['type'],
			'default' => $this->arguments['default'],
			'required' => $this->getFlexFormBoolean($this->arguments['required']),
			'exclude' => $this->getFlexFormBoolean($this->arguments['exclude']),
		);
	}

	/**
	 * Add a field to the currently active/last group (or a new group, if none exist)
	 *
	 * @param array $config
	 */
	protected function addField(array $config) {
		$storage = $this->getStorage();
		$group = array_pop($storage);
		if (!$group) {
			$group = array(
				'name' => 'default',
				'label' => '',
				'fields' => array(),
				'areas' => array()
			);
		}
		array_push($group['fields'], $config);
		array_push($storage, $group);
		$this->setStorage($storage);
	}

	/**
	 * Get 1 or 0 from a boolean
	 *
	 * @param type $value
	 */
	protected function getFlexFormBoolean($value) {
		return ($value === TRUE ? 1 : 0);
	}

}

?>
