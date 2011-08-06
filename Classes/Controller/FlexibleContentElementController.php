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
 * Flexible Content Element Plugin Rendering Controller
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Controller
 */
class Tx_Fed_Controller_FlexibleContentElementController extends Tx_Fed_Core_AbstractController {



	/**
	 * Show template as defined in flexform
	 * @return string
	 */
	public function showAction() {
		$cObj = $this->request->getContentObjectData();
		$this->flexform->setContentObjectData($cObj);
		$configurationManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		list ($extensionName, $filename) = explode(':', $cObj['tx_fed_fcefile']);
		$this->view = $this->objectManager->get('Tx_Fed_View_ExposedTemplateView');
		$this->view->setControllerContext($this->controllerContext);
		if ($extensionName && $filename) {
			$paths = $configurationManager->getContentConfiguration($extensionName);
			$absolutePath = $this->translatePath($paths['templateRootPath']) . DIRECTORY_SEPARATOR . $filename;
			$this->view->setLayoutRootPath($this->translatePath($paths['layoutRootPath']));
			$this->view->setPartialRootPath($this->translatePath($paths['partialRootPath']));
			$this->view->setTemplatePathAndFilename($absolutePath);
		} else {
			$absolutePath = PATH_site . $this->translatePath($paths['templateRootPath']) . DIRECTORY_SEPARATOR . $cObj['tx_fed_fcefile'];
			$this->view->setTemplatePathAndFilename($absolutePath);
		}
		$config = $this->view->getStoredVariable('Tx_Fed_ViewHelpers_FceViewHelper', 'storage', 'Configuration');
		$templateVariables = $this->flexform->getAllAndTransform($config['fields']);
		$templateVariables['page'] = $GLOBALS['TSFE']->page;
		$templateVariables['record'] = $cObj;
		$templateVariables['config'] = $config;
		$content = $this->view->renderStandaloneSection('Main', $templateVariables);
		return $content;
	}

	/**
	 * Translates a TypoScript path into an absolute path
	 * @param string $path
	 * @return string
	 */
	protected function translatePath($path) {
		if (strpos($path, 'EXT:') === 0) {
			$slice = strpos($path, '/');
			$extKey = array_pop(explode(':', substr($path, 0, $slice)));
			$path = t3lib_extMgm::extPath($extKey, substr($path, $slice));
		}
		return $path;
	}

}

?>