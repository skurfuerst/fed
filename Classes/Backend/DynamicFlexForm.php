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
	 * @var Tx_Fed_Backend_FCEParser
	 */
	protected $fceParser;

	/**
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexform;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->fceParser = $this->objectManager->get('Tx_Fed_Backend_FCEParser');
		$this->flexform = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
	}

	public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, &$row, $table, $fieldName) {
		$configurationManager = t3lib_div::makeInstance('Tx_Extbase_Configuration_BackendConfigurationManager');
		$config = $configurationManager->getTypoScriptSetup();
		$config = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($config);
		$typoscript = $config['plugin']['tx_fed']['page'];
		if ($table === 'pages') {
			if ($row['layout'] < 255) {
				return;
			}
			$action = $row['tx_fed_page_controller_action'] ? $row['tx_fed_page_controller_action'] : 'Default';
			if (strpos($action, '->')) {
				list ($extensionName, $action) = explode('->', $action);
			} else {
				$extensionName = 'fed';
			}
			$paths = $typoscript[$extensionName];
			$templatePath = $this->translatePath($paths['templateRootPath']);
			$templateFile = PATH_site . $templatePath . '/Page/' . $action . '.html';
			$this->readFlexFormFields($templateFile, array(), $paths, $dataStructArray, $conf, $row, $table, $fieldName);
		} else if ($row['CType'] == 'fed_fce') {
			$templateFile = PATH_site . $row['tx_fed_fcefile'];
			$values = $this->flexform->convertFlexFormContentToArray($row['pi_flexform']);
			$this->readFlexFormFields($templateFile, $values, $typoscript, $dataStructArray, $conf, $row, $table, $fieldName);
		} else if ($row['CType'] == 'fed_template') {
			$templateFile = t3lib_extMgm::extPath('fed', 'Configuration/FlexForms/Template.xml');
			$dataStructArray = t3lib_div::xml2array(file_get_contents($templateFile));
		} else if ($row['CType'] == 'fed_datasource') {
			$templateFile = t3lib_extMgm::extPath('fed', 'Configuration/FlexForms/DataSource.xml');
			$dataStructArray = t3lib_div::xml2array(file_get_contents($templateFile));
		}

	}

	protected function readFlexFormFields($templateFile, $values, $paths, &$dataStructArray, $conf, &$row, $table, $fieldName) {
		if (is_file($templateFile) === FALSE) {
			$dataStructArray = array('ROOT' => array('type' => 'array', 'el' => array()));
			return;
		}
		$config = $this->fceParser->getFceDefinitionFromTemplate($templateFile, $values, $paths);
		$flexformTemplateFile = t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/Page/AutoFlexForm.xml');
		$template = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
		$template->setTemplatePathAndFilename($flexformTemplateFile);
		$template->setPartialRootPath($paths['partialRootPath']);
		$template->setLayoutRootPath($paths['layoutRootPath']);
		$template->assignMultiple($values);
		$template->assign('fce', $config);
		$flexformXml = $template->render();
		$dataStructArray = t3lib_div::xml2array($flexformXml);
	}

	protected function translatePath($path) {
		if (strpos($path, 'EXT:') === 0) {
			$slice = strpos($path, '/');
			$extKey = array_pop(explode(':', substr($path, 0, $slice)));
			$path = t3lib_extMgm::siteRelPath($extKey) . substr($path, $slice);
		}
		return $path;
	}

}


?>
