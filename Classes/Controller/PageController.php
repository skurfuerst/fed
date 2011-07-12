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
		$config = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$config = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($config);
		$typoscript = $config['plugin']['tx_fed']['page'];
		$view = $this->objectManager->get('Tx_Fluid_View_TemplateView');
		$rootline = array();
		$configuration = $this->getPageTemplateConfiguration($GLOBALS['TSFE']->id, $rootline);
		if ($view instanceof Tx_Fluid_View_TemplateView) {
			$templateRootPath = $this->translatePath($typoscript['templateRootPath']);
			$layoutRootPath = $this->translatePath($typoscript['layoutRootPath']);
			$partialRootPath = $this->translatePath($typoscript['partialRootPath']);
			$view->setControllerContext($this->controllerContext);
			$view->setTemplateRootPath($templateRootPath);
			$view->setLayoutRootPath($layoutRootPath);
			$view->setPartialRootPath($partialRootPath);
			return $view->render($configuration['tx_fed_page_controller_action']);
		}
		return $view->render;
	}

	protected function getPageTemplateConfiguration($id, $rootline) {
		$page = $GLOBALS['TSFE']->page;
		return array(
			'tx_fed_page_controller_action' => $page['tx_fed_page_controller_action'],
			#'tx_fed_page_controller_action_sub' => $page['tx_fed_page_controller_action_sub'],
			'tx_fed_page_format' => $page['tx_fed_page_layout']
		);
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