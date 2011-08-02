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
 * @subpackage ViewHelpers\Data
 */
class Tx_Fed_ViewHelpers_Data_SortViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	public function initializeArguments() {
		$this->registerArgument('as', 'string', 'Which variable to update in the TemplateVariableContainer. If left out, returns sorted data instead of updating the varialbe (i.e. reference or copy)');
		$this->registerArgument('sortBy', 'string', 'Which property/field to sort by - leave out for numeric sorting based on indexes(keys)');
		$this->registerArgument('order', 'string', 'ASC or DESC', FALSE, 'ASC');
	}

	/**
	 * "Render" method - sorts a target list-type target. Either $array or $objectStorage must be specified. If both are,
	 * ObjectStorage takes precedence.
	 *
	 * @param array $array Optional; use to sort an array
	 * @param Tx_Extbase_Persistence_ObjectStorage $objectStorage Optional; use to sort an ObjectStorage
	 * @return mixed
	 */
	public function render($array=NULL, Tx_Extbase_Persistence_ObjectStorage $objectStorage=NULL) {
		if ($objectStorage) {
			$sorted = $this->sortObjectStorage($objectStorage);
		} else if ($array) {
			$sorted = $this->sortArray($array);
		} else {
			throw new Exception('Nothing to sort, SortViewHelper has no purpose in life, performing LATE term self-abortion');
		}
		if ($this->arguments['as']) {
			if ($this->templateVariableContainer->exists($this->arguments['as'])) {
				$this->templateVariableContainer->remove($this->arguments['as']);
			}
			$this->templateVariableContainer->add($this->arguments['as'], $sorted);
			return $this->renderChildren();
		} else {
			return $sorted;
		}
	}

	protected function sortArray($array) {
		$sorted = array();
		while ($object = array_shift($array)) {
			$index = $this->getSortValue($object);
			$sorted[$index] = $object;
		}
		if ($this->arguments['order'] === 'ASC') {
			ksort($sorted);
		} else {
			krsort($sorted);
		}
		return $sorted;
	}

	protected function sortObjectStorage($storage) {
		$temp = $this->objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
		$temp->attachAll($storage);
		$sorted = array();
		foreach ($storage as $item) {
			$index = $this->getSortValue($item);
			$sorted[$index] = $item;
		}
		if ($this->arguments['order'] === 'ASC') {
			ksort($sorted);
		} else {
			krsort($sorted);
		}
		$storage->detachAll($storage);
		foreach ($sorted as $item) {
			$storage->attach($item);
		}
		return $storage;
	}

	protected function getSortValue($object) {
		$field = $this->arguments['sortBy'];
		if ($field) {
			$getter = 'get' . ucfirst($field);
		} else {
			$getter = "getUid";
		}
		if (method_exists($object, $getter)) {
			$value = $object->$getter();
		} else if (is_object ($object)) {
			$value = $object->$field;
		} else if (is_array($object)) {
			$value = $object[$field];
		}
		if ($value instanceof DateTime) {
			$value = $value->getTimestamp();
		}
		return $value;
	}
}

?>