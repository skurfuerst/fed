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
 * @subpackage Backend
 */

class Tx_Fed_Backend_TCEMain {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexFormService;

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Extbase_Reflection_Service
	 */
	protected $reflectionService;

	/**
	 * @var Tx_Extbase_Property_PropertyMapper
	 */
	protected $propertyMapper;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->flexFormService = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
		$this->infoService = $this->objectManager->get('Tx_Fed_Utility_DomainObjectInfo');
		$this->reflectionService = $this->objectManager->get('Tx_Extbase_Reflection_Service');
		$this->propertyMapper = $this->objectManager->get('Tx_Extbase_Property_PropertyMapper');
	}

	/**
	 * @param string $table
	 * @param string $action
	 * @param array $record
	 * @param array $arguments
	 * @return array
	 */
	protected function executeBackendControllerCommand($table, $action, $record, $arguments=array()) {
		$objectType = $this->infoService->getObjectType($table);
		try {
			if ($objectType) {
				$keys = array_keys($record);
				$controllerClassName = $this->infoService->getBackendControllerClassName($objectType);
				if ($controllerClassName) {
					if ($record['uid'] < 1) {
						$object = $this->objectManager->get($objectType);
					} else {
						$repository = $this->infoService->getRepositoryInstance($objectType);
						$query = $repository->createQuery();
						$query->getQuerySettings()->setRespectEnableFields(FALSE);
						$query->getQuerySettings()->setRespectStoragePage(FALSE);
						$object = $query->execute()->getFirst();
					}
					$translatedKeys = $this->infoService->convertLowerCaseUnderscoredToLowerCamelCase($keys);
					$translatedRecordValues = array_combine($translatedKeys, $record);
					foreach ($translatedRecordValues as $underScoredName=>$value) {
						$upperCamelCaseName = Tx_Extbase_Utility_Extension::convertLowerUnderscoreToUpperCamelCase($underScoredName);
						$setter = 'set' . $upperCamelCaseName;
						$methodArray = array($object, $setter);
						if (method_exists($object, $setter)) {
							call_user_func_array($methodArray, array($value));
						}
					}
					$controller = $this->objectManager->get($controllerClassName);
				}
			}
			if ($controller && $object) {
				array_unshift($arguments, $object);
				$method = $action . 'Action';
				if (method_exists($controller, $method)) {
					$object = call_user_func_array(array($controller, $method), $arguments);
					$properties = $this->infoService->getValuesByAnnotation($object, 'var');
					foreach ($properties as $key=>$value) {
						$indexName = $this->infoService->convertCamelCaseToLowerCaseUnderscored($key);
						$record[$indexName] = $value;
					}
				}
			}
		} catch (Exception $e) {
			#return var_dump($e->getMessage());
		}
		#var_dump($record);
		#exit();
		return $record;
	}

	/**
	 * @param	string		$command: The TCEmain operation status, fx. 'update'
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	array		$fieldArray: The field names and their values to be processed
	 * @param	object		$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access	public
	 */
	public function processCmdmap_preProcess (&$command, $table, $id, $value, t3lib_TCEmain &$reference) {

	}

	/**
	 * @param	array		$incomingFieldArray: The original field names and their values before they are processed
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	t3lib_TCEmain	$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access	public
	 */
	public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, $table, $id, t3lib_TCEmain &$reference) {
		if ($incomingFieldArray['uid'] > 0) {
			$action = 'read';
		} else {
			$action = 'create';
		}
		$incomingFieldArray = $this->executeBackendControllerCommand($table, $action, $incomingFieldArray);
	}

	/**
	 * @param	string		$status: The TCEmain operation status, fx. 'update'
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	array		$fieldArray: The field names and their values to be processed
	 * @param	object		$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access	public
	 */
	public function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, t3lib_TCEmain &$reference) {
		$record = $this->executeBackendControllerCommand($table, $status, $fieldArray);
		if ($record) {
			$fieldArray = $record;
		}
	}

	/**
	 * @param	string		$status: The command which has been sent to processDatamap
	 * @param	string		$table:	The table we're dealing with
	 * @param	mixed		$id: Either the record UID or a string if a new record has been created
	 * @param	array		$fieldArray: The record row how it has been inserted into the database
	 * @param	object		$reference: A reference to the TCEmain instance
	 * @return	void
	 */
	public function processDatamap_afterDatabaseOperations ($status, $table, $id, $fieldArray, &$reference) {

	}

	/**
	 * This function is called by TCEmain after a record has been moved to the first position of
	 * the page. We make sure that this is also reflected in the pages references.
	 *
	 * @param	string		$table:	The table we're dealing with
	 * @param	integer		$uid: The record UID
	 * @param	integer		$destPid: The page UID of the page the element has been moved to
	 * @param	array		$sourceRecordBeforeMove: (A part of) the record before it has been moved (and thus the PID has possibly been changed)
	 * @param	array		$updateFields: The updated fields of the record row in question (we don't use that)
	 * @param	object		$reference: A reference to the TCEmain instance
	 * @return	void
	 */
	public function moveRecord_firstElementPostProcess ($table, $uid, $destPid, $sourceRecordBeforeMove, $updateFields, &$reference) {

	}

	/**
	 * This function is called by TCEmain after a record has been moved to after another record on some
	 * the page. We make sure that this is also reflected in the pages references.
	 *
	 * @param	string		$table:	The table we're dealing with
	 * @param	integer		$uid: The record UID
	 * @param	integer		$destPid: The page UID of the page the element has been moved to
	 * @param	integer		$origDestPid: The "original" PID: This tells us more about after which record our record wants to be moved. So it's not a page uid but a tt_content uid!
	 * @param	array		$sourceRecordBeforeMove: (A part of) the record before it has been moved (and thus the PID has possibly been changed)
	 * @param	array		$updateFields: The updated fields of the record row in question (we don't use that)
	 * @param	object		$reference: A reference to the TCEmain instance
	 * @return	void
	 */
	public function moveRecord_afterAnotherElementPostProcess ($table, $uid, $destPid, $origDestPid, $sourceRecordBeforeMove, $updateFields, &$reference) {

	}

}
?>