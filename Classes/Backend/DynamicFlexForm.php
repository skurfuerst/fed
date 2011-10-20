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
*  the Free Software Foundation; either version 3 of the License, or
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
 * Dynamic FlexForm insertion hook class
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Backend
 */
class Tx_Fed_Backend_DynamicFlexForm {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @var Tx_Fed_Configuration_ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexform;

	/**
	 * @var Tx_Fed_Service_Page
	 */
	protected $pageService;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->configurationManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		$this->flexform = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
		$this->pageService = $this->objectManager->get('Tx_Fed_Service_Page');
	}

	/**
	 * Hook for generating dynamic FlexForm source code
	 *
	 * @param array $dataStructArray
	 * @param array $conf
	 * @param array $row
	 * @param string $table
	 * @param string $fieldName
	 */
	public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, &$row, $table, $fieldName) {
		if ($table === 'pages' && $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFluidPageTemplates']) {
			$configuration = $this->pageService->getPageTemplateConfiguration($row['uid']);
			if ($configuration['tx_fed_page_controller_action']) {
				$action = $configuration['tx_fed_page_controller_action'];
				list ($extensionName, $action) = explode('->', $action);
				$paths = $this->configurationManager->getPageConfiguration($extensionName);
				$templatePath = Tx_Fed_Utility_Path::translatePath($paths['templateRootPath']);
				$templateFile = $templatePath . '/Page/' . $action . '.html';
				if (file_exists($templateFile) === FALSE) {
					throw new Exception('Invalid template file selected - file does not exist: ' . $templateFile, 1318783138);
				}
				$pageFlexFormSource = $this->pageService->getPageFlexFormSource($row['uid']);
				$values = $this->flexform->convertFlexFormContentToArray($pageFlexFormSource);
				$this->flexform->convertFlexFormContentToDataStructure($templateFile, $values, $paths, $dataStructArray, $conf, $row, $table, $fieldName);
			}
		} else if ($row['CType'] == 'fed_fce') {
			list ($extensionName, $filename) = explode(':', $row['tx_fed_fcefile']);
			$values = $this->flexform->convertFlexFormContentToArray($row['pi_flexform']);
			$paths = $this->configurationManager->getContentConfiguration($extensionName);
			if ($paths) {
				$filename = $paths['templateRootPath'] . $filename;
				$filename = Tx_Fed_Utility_Path::translatePath($filename);
			} else {
				$filename = $row['tx_fed_fcefile'];
			}
			$this->flexform->convertFlexFormContentToDataStructure($filename, $values, $paths, $dataStructArray, $conf, $row, $table, $fieldName);
		} else if ($row['CType'] == 'fed_template') {
			$templateFile = t3lib_extMgm::extPath('fed', 'Configuration/FlexForms/Template.xml');
			$dataStructArray = t3lib_div::xml2array(file_get_contents($templateFile));
		} else if ($row['CType'] == 'fed_datasource') {
			$templateFile = t3lib_extMgm::extPath('fed', 'Configuration/FlexForms/DataSource.xml');
			$dataStructArray = t3lib_div::xml2array(file_get_contents($templateFile));
		} else {
				// check for registered Fluid FlexForms based on cType first, then plugin list_type
			$flexFormConfiguration = Tx_Fed_Core::getRegisteredFlexForms('contentObject', $row['cType']);
			if (!$flexFormConfiguration && $row['list_type']) {
				$flexFormConfiguration = Tx_Fed_Core::getRegisteredFlexForms('plugin', $row['list_type']);
			}
			if ($flexFormConfiguration) {
				$values = $this->flexform->convertFlexFormContentToArray($row['pi_flexform']);
				$this->flexform->convertFlexFormContentToDataStructure($flexFormConfiguration['templateFilename'], $values, $paths, $dataStructArray, $conf, $row, $table, $fieldName);
			}
		}
	}

}

?>