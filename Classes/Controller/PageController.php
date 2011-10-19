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
	 * @var Tx_Fed_Service_Page
	 */
	protected $pageService;

	/**
	 * @param Tx_Fed_Service_Page $pageLayout
	 */
	public function injectPageService(Tx_Fed_Service_Page $pageService) {
		$this->pageService = $pageService;
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
		$configManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		$configuration = $this->pageService->getPageTemplateConfiguration($GLOBALS['TSFE']->id);
		$flexFormSource = $this->pageService->getPageFlexFormSource($GLOBALS['TSFE']->id);
		$flexformData = $this->flexform->convertFlexFormContentToArray($flexFormSource);
		list ($extensionName, $action) = explode('->', $configuration['tx_fed_page_controller_action']);
		$paths = $configManager->getPageConfiguration($extensionName);
		$templateRootPath = Tx_Fed_Utility_Path::translatePath($paths['templateRootPath']);
		$layoutRootPath = Tx_Fed_Utility_Path::translatePath($paths['layoutRootPath']);
		$partialRootPath = Tx_Fed_Utility_Path::translatePath($paths['partialRootPath']);
		$view = $this->objectManager->get('Tx_Fluid_View_TemplateView');
		$view->setControllerContext($this->controllerContext);
		$view->setTemplateRootPath($templateRootPath);
		$view->setLayoutRootPath($layoutRootPath);
		$view->setPartialRootPath($partialRootPath);
		$view->assignMultiple($flexformData);
		$view->assign('page', $GLOBALS['TSFE']->page);
		$view->assign('user', $GLOBALS['TSFE']->fe_user->user);
		$view->assign('cookies', $_COOKIE);
		$view->assign('session', $_SESSION);
		return $view->render($action);
	}

}

?>