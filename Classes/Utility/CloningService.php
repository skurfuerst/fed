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
 * @subpackage Utility
 */
class Tx_Fed_Utility_CloningService implements t3lib_Singleton {
	
	/**
	 * RecursionHandler instance
	 * @var Tx_Fed_Utility_RecursionHandler
	 */
	public $recursionHandler;
	
	/**
	 * ReflectionService instance
	 * @var Tx_Extbase_Reflection_Service $service
	 */
	protected $reflectionService;
	
	/**
	 * ObjectManager instance
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManger;
	
	/**
	 * Inject a RecursionHandler instance
	 * @param Tx_Fed_Utility_RecursionHandler $handler
	 */
	public function injectRecursionHandler(Tx_Fed_Utility_RecursionHandler $handler) {
		$this->recursionHandler = $handler;
	}
	
	/**
	 * Inject a Reflection Service instance
	 * @param Tx_Extbase_Reflection_Server $service
	 */
	public function injectReflectionService(Tx_Extbase_Reflection_Service $service) {
		$this->reflectionService = $service;
	}
	
	/**
	 * Inject a Reflection Service instance
	 * @param Tx_Extbase_Object_ObjectManager $manager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $manager) {
		$this->objectManager = $manager;
	}
	
	/**
	 * Copy a singe object based on field annotations about how to copy the object
	 * 
	 * @return Tx_Extbase_DomainObject_AbstractDomainOject $copy
	 */
	public function copy($object) {
		$className = get_class($object);
		$this->recursionHandler->in();
		$this->recursionHandler->check($className);
		$copy = $this->objectManager->get($className);
		$properties = $this->reflectionService->getClassPropertyNames($className);
		foreach ($properties as $propertyName) {
			$tags = $this->reflectionService->getPropertyTagsValues($className, $propertyName);
			$getter = 'get' . ucfirst($propertyName);
			$setter = 'set' . ucfirst($propertyName);
			$copyMethod = $tags['copy'][0];
			if ($copyMethod !== NULL && $copyMethod !== 'ignore') {
				$originalValue = $object->$getter();
				if ($copyMethod == 'reference') {
					$copiedValue = $this->copyAsReference($originalValue);
				} else if ($copyMethod == 'clone') {
					$copiedValue = $this->copyAsClone($originalValue);
				}
				if ($copiedValue != NULL) {
					$copy->$setter($copiedValue);
				}
			}
		}
		$this->recursionHandler->out();
		return $copy;
	}
	
	protected function copyAsReference($value) {
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		if ($value instanceof Tx_Extbase_Persistence_ObjectStorage) {
			// objectstorage; copy storage and attach items to this new storage
			// if 1:n mapping is used, items are detached from their old storage - this is 
			// a limitation of this type of reference
			$newStorage = $this->objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
			foreach ($value as $item) {
				$newStorage->attach($item);
			}
			return $newStorage;
		} else if ($value instanceof Tx_Locus_Domain_Model_AbstractDomainModelObject) {
			// 1:1 mapping as reference; return object itself
			return $value;
		} else if (is_object($value)) {
			// fallback case for class copying - value objects and such
			return $value;
		} else {
			// this case is very unlikely: means someone wished to copy hard type as a reference - so return a copy instead
			return $value;
		}
	}
	
	protected function copyAsClone($value) {
		if ($value instanceof Tx_Extbase_Persistence_ObjectStorage) {
			// objectstorage; copy storage and copy items, return new storage
			$newStorage = $this->objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
			foreach ($value as $item) {
				$newItem = $this->copy($item);
				$newStorage->attach($newItem);
			}
			return $newStorage;
		} else if ($value instanceof Tx_Locus_Domain_Model_AbstractDomainModelObject) {
			// DomainObject; copy and return
			return $this->copy($value);
		} else if (is_object($value)) {
			// fallback case for class copying - value objects and such
			return clone $value;
		} else {
			// value is probably a string
			return $value;
		}
	}
	
}

?>