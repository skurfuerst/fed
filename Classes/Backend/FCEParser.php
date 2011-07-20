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
 * ************************************************************* */

/**
 *
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Backend
 */
class Tx_Fed_Backend_FCEParser implements t3lib_Singleton {

	/**
	 * @var Tx_Fed_View_FlexibleContentElementView
	 */
	protected $view;

	/**
	 * @var type Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 *
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->view = $this->objectManager->get('Tx_Fed_View_FlexibleContentElementView');
	}

	public function getFceDefinitionFromTemplate($templateFile, $data=NULL, $paths=array()) {
		$this->view->setTemplatePathAndFilename($templateFile);
		if (is_array($data)) {
			$this->view->assignMultiple($data);
		}
		if ($paths) {
			$this->view->setPartialRootPath(PATH_site . $this->translatePath($paths['partialRootPath']));
			$this->view->setLayoutRootPath(PATH_site . $this->translatePath($paths['layoutRootPath']));
		}
		return $this->view->getFlexibleContentElementDefinitions();
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