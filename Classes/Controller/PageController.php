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
 * Page Controller
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Controller
 */
class Tx_Fed_Controller_PageController extends Tx_Fed_Core_AbstractController {

	/**
	 * @var Tx_Fed_Utility_PageLayout
	 */
	protected $pageLayout;

	/**
	 * @param Tx_Fed_Utility_PageLayout $pageLayout
	 */
	public function injectPageLayout(Tx_Fed_Utility_PageLayout $pageLayout) {
		$this->pageLayout = $pageLayout;
	}

	/**
	 * @return string
	 */
	public function renderAction() {

	}

	/**
	 *
	 * @return string
	 */
	public function listAction() {
		$flexform = $this->flexform->convertFlexFormContentToArray($GLOBALS['TSFE']->page['tx_fed_page_flexform']);
		$config = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$config = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($config);
		$typoscript = $config['plugin']['tx_fed']['page'];
		$view = $this->objectManager->get('Tx_Fluid_View_TemplateView');
		$rootline = array();
		$configuration = $this->getPageTemplateConfiguration($GLOBALS['TSFE']->id, $rootline);
		if (strpos($configuration['tx_fed_page_controller_action'], '->')) {
			list ($extensionName, $action) = explode('->', $configuration['tx_fed_page_controller_action']);
		} else {
			$extensionName = 'fed';
			$action = $configuration['tx_fed_page_controller_action'];
		}
		$templates = $this->getTyposcript();
		$paths = $templates[$extensionName];
		$templateRootPath = $this->translatePath($paths['templateRootPath']);
		$layoutRootPath = $this->translatePath($paths['layoutRootPath']);
		$partialRootPath = $this->translatePath($paths['partialRootPath']);
		$view->setControllerContext($this->controllerContext);
		$view->setTemplateRootPath($templateRootPath);
		$view->setLayoutRootPath($layoutRootPath);
		$view->setPartialRootPath($partialRootPath);
		$view->assignMultiple($flexform);
		$view->assign('page', $GLOBALS['TSFE']->page);
		$view->assign('user', $GLOBALS['TSFE']->fe_user->user);
		$view->assign('cookies', $_COOKIE);
		$view->assign('session', $_SESSION);
		return $view->render($action);
	}

	protected function getPageTemplateConfiguration($id, $rootline) {
		$page = $GLOBALS['TSFE']->page;
		return array(
			'tx_fed_page_controller_action' => $page['tx_fed_page_controller_action'],
			'tx_fed_page_format' => $page['tx_fed_page_layout']
		);
	}

	protected function translatePath($path) {
		if (strpos($path, 'EXT:') === 0) {
			$slice = strpos($path, '/');
			$extKey = array_pop(explode(':', substr($path, 0, $slice)));
			$path = t3lib_extMgm::extPath($extKey, substr($path, $slice));
		}
		return $path;
	}

	/**
	 *
	 * @TODO This is duplicated code. Should be moved to a Service in the future
	 * @param type $format
	 * @return type
	 */
	protected function getAvailablePageTemplates($format) {
		$typoscript = $this->getTyposcript();
		$output = array();
		foreach ($typoscript as $extensionName=>$group) {
			$path = $group['templateRootPath'];
			$path = $this->translatePath($path);
			$dir = PATH_site . $path;
			$files = scandir($dir);
			$output[$extensionName] = array();
			foreach ($files as $k=>$file) {
				$pathinfo = pathinfo($dir . $file);
				$extension = $pathinfo['extension'];
				if (substr($file, 0, 1) === '.') {
					unset($files[$k]);
				} else if (strtolower($extension) != strtolower($format)) {
					unset($files[$k]);
				} else {
					$output[$extensionName][] = $pathinfo['filename'];
				}
			}
		}
		return $output;
	}

	/**
	 * Get typoscript definition for Page Templates
	 *
	 * @return array
	 */
	protected function getTyposcript() {
		$configManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		return $configManager->getPageConfiguration();
	}

}

?>