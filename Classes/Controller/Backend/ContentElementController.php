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
 * ContentElementController
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Controller/Backend
 */
class Tx_Fed_Controller_Backend_ContentElementController extends Tx_Fed_MVC_Controller_AbstractBackendController {

	/**
	 * @var Tx_Fed_Domain_Repository_ContentElementRepository
	 */
	protected $contentElementRepository;

	/**
	 * @param Tx_Fed_Domain_Repository_ContentElementRepository $contentElementRepository
	 */
	public function injectContentElementRepository(Tx_Fed_Domain_Repository_ContentElementRepository $contentElementRepository) {
		$this->contentElementRepository = $contentElementRepository;
	}

	/**
	 * @param Tx_Fed_Domain_Model_ContentElement $contentElement
	 * @return Tx_Fed_Domain_Model_ContentElement
	 */
	public function createAction(Tx_Fed_Domain_Model_ContentElement $contentElement) {
		$url = t3lib_div::_GET('returnUrl');
		$editing = t3lib_div::_GET('edit');
		$uid = key($editing['tt_content']);
		if ($uid < 0) {
			$uid = abs($uid);
			$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('pid,sorting', 'tt_content', "uid = '{$uid}'");
			$pid = $record['pid'];
			$sorting = $record['sorting'] + 1;
		} else {
			$pid = $uid;
			$sorting = -1;
		}
		$area = $this->getContentAreaFromUrlOrPageUid($url);
		$contentElement->setTxFedFcecontentarea($area);
		$contentElement->setSorting($sorting);
		$contentElement->setPid($pid);
		return $contentElement;
	}

	/**
	 * @param Tx_Fed_Domain_Model_ContentElement $contentElement
	 * @return Tx_Fed_Domain_Model_ContentElement
	 */
	public function updateAction(Tx_Fed_Domain_Model_ContentElement $contentElement) {
		return $contentElement;
	}

	/**
	 * @param Tx_Fed_Domain_Model_ContentElement $contentElement
	 * @return Tx_Fed_Domain_Model_ContentElement
	 */
	public function readAction(Tx_Fed_Domain_Model_ContentElement $contentElement) {
		return $contentElement;
	}

	/**
	 * @param Tx_Fed_Domain_Model_ContentElement $contentElement
	 * @return Tx_Fed_Domain_Model_ContentElement
	 */
	public function deleteAction(Tx_Fed_Domain_Model_ContentElement $contentElement) {
		return $contentElement;
	}

	/**
	 * @param Tx_Fed_Domain_Model_ContentElement $contentElement
	 * @param integer $relativeRecordUid Positive (after) or negative (before) integer UID of relative record
	 * @return Tx_Fed_Domain_Model_ContentElement
	 */
	public function moveAction(Tx_Fed_Domain_Model_ContentElement $contentElement, $relativeRecordUid) {
		return $contentElement;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	protected function getContentAreaFromUrlOrPageUid($url) {
		$urlHashCutoffPoint = strrpos($url, '#');
		if ($urlHashCutoffPoint > 0) {
			$area = substr($url, 1 - (strlen($url)-$urlHashCutoffPoint));
		}
		return $area;
	}


}

?>