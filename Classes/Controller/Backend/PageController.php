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
 * PageController
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Controller/Backend
 */
class Tx_Fed_Controller_Backend_PageController extends Tx_Fed_MVC_Controller_AbstractBackendController {

	/**
	 * @var Tx_Fed_Domain_Repository_PageRepository
	 */
	protected $pageRepository;

	/**
	 * @param Tx_Fed_Domain_Repository_PageRepository $pageRepository
	 */
	public function injectPageRepository(Tx_Fed_Domain_Repository_PageRepository $pageRepository) {
		$this->pageRepository = $pageRepository;
	}

	/**
	 * @param Tx_Fed_Domain_Model_Page $page
	 * @return Tx_Fed_Domain_Model_Page
	 */
	public function createAction(Tx_Fed_Domain_Model_Page $page) {
		return $page;
	}

	/**
	 * @param Tx_Fed_Domain_Model_Page $page
	 * @return Tx_Fed_Domain_Model_Page
	 */
	public function readAction(Tx_Fed_Domain_Model_Page $page) {
		return $page;
	}

	/**
	 * @param Tx_Fed_Domain_Model_Page $page
	 * @return Tx_Fed_Domain_Model_Page
	 */
	public function updateAction(Tx_Fed_Domain_Model_Page $page) {
		die('test');
		return $page;
	}

	/**
	 * @param Tx_Fed_Domain_Model_Page $page
	 * @return Tx_Fed_Domain_Model_Page
	 */
	public function deleteAction(Tx_Fed_Domain_Model_Page $page) {
		return $page;
	}

	/**
	 * @param Tx_Fed_Domain_Model_Page $page
	 * @return Tx_Fed_Domain_Model_Page
	 * @param integer $relativeRecordUid Positive (after) or negative (before) integer UID of relative record
	 */
	public function moveAction(Tx_Fed_Domain_Model_Page $page, $relativeRecordUid) {
		return $page;
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


}

?>