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


require_once t3lib_extMgm::extPath('cms', 'layout/interfaces/interface.tx_cms_layout_tt_content_drawitemhook.php');

/**
 * Flexible Content Element Backend Renderer
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Backend
 */
class Tx_Fed_Backend_Preview implements tx_cms_layout_tt_content_drawItemHook {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @var Tx_Fluid_View_StandaloneView
	 */
	protected $view;

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$templatePathAndFilename = t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/FlexibleContentElement/BackendPreview.html');
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->jsonService = $this->objectManager->get('Tx_Fed_Utility_JSON');
		$this->view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
		$this->view->setTemplatePathAndFilename($templatePathAndFilename);
	}

	/**
	 * Preprocessing
	 *
	 * @param tx_cms_layout $parentObject
	 * @param boolean $drawItem
	 * @param type $headerContent
	 * @param type $itemContent
	 * @param array $row
	 */
	public function preProcess(tx_cms_layout &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
		switch ($row['CType']) {
			case 'fed_fce': $this->preProcessFlexibleContentElement($drawItem, $itemContent, $row); break;
			case 'fed_template': $this->preProcessTemplateDisplay($drawItem, $itemContent, $row); break;
			default: break;
		}
	}

	protected function preProcessTemplateDisplay(&$drawItem, &$itemContent, array &$row) {
		$flexform = t3lib_div::xml2array($row['pi_flexform']);
		$templateFile = $flexform['data']['sDEF']['lDEF']['templateFile']['vDEF'];
		$templateSource = $flexform['data']['sDEF']['lDEF']['templateSource']['vDEF'];
		$fluidVars = $this->jsonService->decode($flexform['data']['sDEF']['lDEF']['fluidVars']['vDEF']);
		if (is_file($templateFile)) {
			$this->view->setTemplatePathAndFilename(PATH_site . $templateFile);
		} else if (strlen(trim($templateSource)) > 0) {
			$this->view->setTemplateSource($templateSource);
		}
		if ($fluidVars) {
			if (is_array($fluidVars)) {
				$vars = $fluidVars;
			} else {
				$vars = array();
				foreach ($fluidVars as $k=>$v) {
					$vars[$k] = $v;
				}
			}
			$this->view->assignMultiple($vars);
		}
		$itemContent = $this->view->render();
	}

	protected function preProcessFlexibleContentElement(&$drawItem, &$itemContent, array &$row) {
		$fceTemplateFile = $row['tx_fed_fcefile'];
		$fceTemplateFile = PATH_site . $fceTemplateFile;
		if (is_file($fceTemplateFile)) {
			$drawItem = FALSE;
			$fceParser = $this->objectManager->get('Tx_Fed_Backend_FCEParser');
			$stored = $fceParser->getFceDefinitionFromTemplate($fceTemplateFile);
			foreach ($stored as $groupIndex=>$group) {
				foreach ($group['fields'] as $fieldIndex=>$field) {
					$value = $flexform['data']['sDEF']['lDEF'][$field['name']]['vDEF'];
					$value = strip_tags($value);
					$stored[$groupIndex]['fields'][$fieldIndex]['value'] = $value;
				}
			}
			$this->view->assign('fce', $stored);
			$itemContent = $this->view->render();
		}
	}

}


?>