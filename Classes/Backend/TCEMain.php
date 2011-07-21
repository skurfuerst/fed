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
	 * @var Tx_Fed_Backend_FCEParser
	 */
	protected $fceParser;

	/**
	 *
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexFormService;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->fceParser = $this->objectManager->get('Tx_Fed_Backend_FCEParser');
		$this->flexFormService = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
	}

	/**
	 * This method is called by a hook in the TYPO3 Core Engine (TCEmain).
	 *
	 * @param	string		$status: The TCEmain operation status, fx. 'update'
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
	 * This method is called by a hook in the TYPO3 Core Engine (TCEmain). If a tt_content record is
	 * going to be processed, this function saves the "incomingFieldArray" for later use in some
	 * post processing functions (see other functions below).
	 *
	 * @param	array		$incomingFieldArray: The original field names and their values before they are processed
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	t3lib_TCEmain	$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access	public
	 */
	public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, $table, $id, t3lib_TCEmain &$reference) {
		if ($table == 'tt_content') {
			$targetRelative = $incomingFieldArray['pid'];
			$before = $targetRelative < 0;
			$uid = abs($targetRelative);
			$url = $_GET['returnUrl'];
			$rpos = strrpos($url, '#');
			if ($_GET['id'] > 0) {
				$pid = $_GET['id'];
			} else {
				$pid = key($_GET['edit']['tt_content']);
			}
			if ($rpos > 0 && $uid) {
				$area = substr($url, 1 - (strlen($url)-$rpos));
				$incomingFieldArray['tx_fed_fcecontentarea'] = $area . ':' . $uid;
				$incomingFieldArray['pid'] = $this->getPageUidFromTable($table, $uid);
			} else if ($uid > 0) {
				$incomingFieldArray['tx_fed_fcecontentarea'] = $this->getFceContentAreaFromTable($table, $uid);
				#$incomingFieldArray['pid'] = $pid ? $pid : $_GET['id'];
			}
			#var_dump($incomingFieldArray);
			#exit();
		}
	}

	/**
	 * This method is called by a hook in the TYPO3 Core Engine (TCEmain).
	 *
	 * If a record from table "pages" is created or updated with a new DS but no TO is selected, this function
	 * tries to find a suitable TO and adds it to the fieldArray.
	 *
	 * @param	string		$status: The TCEmain operation status, fx. 'update'
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	array		$fieldArray: The field names and their values to be processed
	 * @param	object		$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access	public
	 */
	public function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, t3lib_TCEmain &$reference) {
		if ($table === 'pages') {
			$this->autoFillPageTemplateDefinition($fieldArray, $table, $id);
		}
	}

	/**
	 * This function is called by TCEmain after a new record has been inserted into the database.
	 * If a new content element has been created, we make sure that it is referenced by its page.
	 *
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

	/**
	 * @param array $incomingFieldArray
	 * @param string $table
	 * @param integer $id
	 */
	protected function getInheritedFlexformConfig(array &$incomingFieldArray, $table, $id) {
		if ($incomingFieldArray['tx_fed_page_controller_action'] == '') {
			$rootLine = $this->getRootLine($id);
			foreach ($rootLine as $row) {
				if ($row['tx_fed_page_flexform'] != '') {
					$incomingFieldArray['tx_fed_page_controller_action'] = $row['tx_fed_page_controller_action_sub'];
					$incomingFieldArray['tx_fed_page_flexform'] = $row['tx_fed_page_flexform'];
					break;
				}
			}
		}
	}

	/**
	 * @param array $incomingFieldArray
	 * @param string $table
	 * @param integer $id
	 */
	protected function autoFillPageTemplateDefinition(array &$incomingFieldArray, $table, $id) {
		if ($incomingFieldArray['tx_fed_page_controller_action'] == '') {
			$rootLine = $this->getRootLine($id);
			foreach ($rootLine as $row) {
				if ($row['tx_fed_page_controller_action_sub'] != '') {
					$incomingFieldArray['tx_fed_page_controller_action'] = $row['tx_fed_page_controller_action_sub'];
					$incomingFieldArray['tx_fed_page_flexform'] = $row['tx_fed_page_flexform'];
					break;
				}
			}
		}
	}

	/**
	 * @param array $incomingFieldArray
	 * @param string $table
	 * @param integer $id
	 */
	protected function getRootLine($id, &$collected=array()) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', "uid = '{$id}'");
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row['pid'] > 0) {
			$this->getRootLine($row['pid'], $collected);
		}
		return $collected;
	}

	/**
	 * @param string $table
	 * @param integer $uid
	 */
	protected function getPageUidFromTable($table, $uid) {
		$pid = 0;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid', $table, "uid = '{$uid}'");
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		return $row['pid'] ? $row['pid'] : $pid;
	}

	/**
	 * @param string $table
	 * @param integer $uid
	 */
	protected function getFceContentAreaFromTable($table, $uid) {
		$area = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_fed_fcecontentarea', $table, "uid = '{$uid}'");
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		return $row['tx_fed_fcecontentarea'] ? $row['tx_fed_fcecontentarea'] : $area;
	}

}