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
 * Service for interacting with Pages
 *
 * @package Fed
 * @subpackage Service
 * @version
 */
class Tx_Fed_Service_Page implements t3lib_Singleton {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var Tx_Fed_Domain_Repository_ContentElementRepository
	 * @inject
	 */
	protected $contentElementRepository;

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param Tx_Fed_Domain_Repository_ContentElementRepository $contentElementRepository
	 */
	public function injectContentElementRepository(Tx_Fed_Domain_Repository_ContentElementRepository $contentElementRepository) {
		$this->contentElementRepository = $contentElementRepository;
	}

	/**
	 * Fetches ContentElement objects from $page where column position matches $columnPosition
	 *
	 * @param Tx_Fed_Domain_Model_Page $page
	 * @param integer $columnPosition
	 */
	public function getContentElementsByColumnPosition(Tx_Fed_Domain_Model_Page $page, $columnPosition) {
		$pid = $page->getUid();
		return $this->contentElementRepository->findAllByPidAndColPos($pid, $columnPosition);
	}

	/**
	 * Fetches ContentElement objects from $page where column name matches $coumnName
	 *
	 * @param Tx_Fed_Domain_Model_Page $page
	 * @param string $columnName
	 */
	public function getContentElementsByColumnName(Tx_Fed_Domain_Model_Page $page, $columnName) {
		$columns = $this->getColumnConfiguration($page);
		$pid = $page->getUid();
		foreach ($columns as $columnPosition=>$column) {
			if ($column['name'] == $columnName) {
				return $this->contentElementRepository->findAllByPidAndColPos($pid, $columnPosition);
			}
		}
		return NULL;
	}

	/**
	 * Gets an array of the column definition in a BackendLayout object
	 *
	 * @param Tx_Fed_Domain_Model_Page $page
	 */
	public function getColumnConfiguration(Tx_Fed_Domain_Model_Page $page) {
		$config = $page->getBackendLayout()->getConfig();
		$parser = $this->objectManager->get('t3lib_tsparser');
		$parser->parse($config);
		$array = $parser->setup;
		$columns = array();
		foreach ($array['rows'] as $row) {
			foreach ($row['columns'] as $column) {
				$columns[$column['colPos']] = $column['name'];
			}
		}
		return $columns;
	}


}
?>