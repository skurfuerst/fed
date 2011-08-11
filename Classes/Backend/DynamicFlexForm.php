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
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->configurationManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		$this->flexform = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
	}

	public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, &$row, $table, $fieldName) {
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
			$paths = $this->configurationManager->getPageConfiguration($extensionName);
			$templatePath = $this->translatePath($paths['templateRootPath']);
			$templateFile = $templatePath . '/Page/' . $action . '.html';
			$values = $this->flexform->convertFlexFormContentToArray($row['tx_fed_page_flexform']);
			$this->readFlexFormFields($templateFile, $values, $paths, $dataStructArray, $conf, $row, $table, $fieldName);
		} else if ($row['CType'] == 'fed_fce') {
			list ($extensionName, $filename) = explode(':', $row['tx_fed_fcefile']);
			$values = $this->flexform->convertFlexFormContentToArray($row['pi_flexform']);
			$paths = $this->configurationManager->getContentConfiguration($extensionName);
			if ($paths) {
				$filename = $paths['templateRootPath'] . $filename;
				$filename = $this->translatePath($filename);
			} else {
				$filename = $row['tx_fed_fcefile'];
			}
			$this->readFlexFormFields($filename, $values, $typoscript, $dataStructArray, $conf, $row, $table, $fieldName);
		} else if ($row['CType'] == 'fed_template') {
			$templateFile = t3lib_extMgm::extPath('fed', 'Configuration/FlexForms/Template.xml');
			$dataStructArray = t3lib_div::xml2array(file_get_contents($templateFile));
		} else if ($row['CType'] == 'fed_datasource') {
			$templateFile = t3lib_extMgm::extPath('fed', 'Configuration/FlexForms/DataSource.xml');
			$dataStructArray = t3lib_div::xml2array(file_get_contents($templateFile));
		}

	}

	protected function readFlexFormFields($templateFile, $values, $paths, &$dataStructArray, $conf, &$row, $table, $fieldName) {
		$onInvalid = array('ROOT' => array('type' => 'array', 'el' => array('void' => array('config' => 'input', 'default' => $templateFile))));
		if (is_file(PATH_site . $templateFile) === FALSE) {
			$dataStructArray = $onInvalid;
			return;
		}
		try {
			$view = $this->objectManager->get('Tx_Fed_View_ExposedTemplateView');
			$view->setTemplatePathAndFilename(PATH_site . $templateFile);
			$view->assignMultiple($values);
			$config = $view->getStoredVariable('Tx_Fed_ViewHelpers_FceViewHelper', 'storage', 'Configuration');
			$groups = array();
			foreach ($config['fields'] as $field) {
				$groupKey = $field['group']['name'];
				$groupLabel = $field['group']['label'];
				if (is_array($groups[$groupKey]) === FALSE) {
					$groups[$groupKey] = array(
						'name' => $groupKey,
						'label' => $groupLabel,
						'fields' => array()
					);
				}
				array_push($groups[$groupKey]['fields'], $field);
			}
			$flexformTemplateFile = t3lib_extMgm::extPath('fed', 'Resources/Private/Partials/AutoFlexForm.xml');
			$template = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
			$template->setTemplatePathAndFilename($flexformTemplateFile);
			$template->setPartialRootPath($paths['partialRootPath']);
			$template->setLayoutRootPath($paths['layoutRootPath']);
			$template->assignMultiple($values);
			$template->assignMultiple($config);
			$template->assign('groups', $groups);
			$flexformXml = $template->render();
			$dataStructArray = t3lib_div::xml2array($flexformXml);
		} catch (Exception $e) {
			$dataStructArray = $onInvalid;
		}
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
