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
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexform;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$templatePathAndFilename = t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/FlexibleContentElement/BackendPreview.html');
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->jsonService = $this->objectManager->get('Tx_Fed_Utility_JSON');
		$this->flexform = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
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
		#$itemContent = $this->view->render();
	}

	protected function preProcessFlexibleContentElement(&$drawItem, &$itemContent, array &$row) {
		$fceTemplateFile = $row['tx_fed_fcefile'];
		$fceTemplateFile = PATH_site . $fceTemplateFile;
		$flexform = $this->flexform->convertFlexFormContentToArray($row['pi_flexform']);
		if (is_file($fceTemplateFile)) {
			try {
				$pageRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', "uid = '{$row['pid']}'");
				$pageRecord = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pageRes);
				$dblist = t3lib_div::makeInstance('tx_cms_layout');
				$dblist->backPath = $GLOBALS['BACK_PATH'];
				$dblist->thumbs = $this->imagemode;
				$dblist->script = 'db_layout.php';
				$dblist->showIcon = 0;
				$dblist->setLMargin = 0;
				$dblist->doEdit = TRUE;
				$dblist->ext_CALC_PERMS = $GLOBALS['BE_USER']->calcPerms($pageRecord);
				$dblist->id = $row['pid'];
				$dblist->nextThree = 0;
				$fceParser = $this->objectManager->get('Tx_Fed_Backend_FCEParser');
				$areas = array();
				$stored = $fceParser->getFceDefinitionFromTemplate($fceTemplateFile, $flexform);
				foreach ($stored as $groupIndex=>$group) {
					foreach ($group['fields'] as $fieldIndex=>$field) {
						$value = $flexform[$field['name']];
						$value = strip_tags($value);
						$stored[$groupIndex]['fields'][$fieldIndex]['value'] = $value;
					}
					foreach ($group['areas'] as $areaIndex=>$area) {
						$areas[$area['name']]['records'] = array();
						$stored[$groupIndex]['areas'][$areaIndex]['records'] = array();
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_content',
								"colPos = '255' AND tx_fed_fcecontentarea = '{$area['name']}:{$row['uid']}' AND deleted = 0", 'uid', 'sorting ASC');
						while ($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
							$dblist->tt_contentData['nextThree'][$record['uid']] = $record['uid'];
							$rendered = $dblist->tt_content_drawHeader($record, 15, TRUE, FALSE);
							$rendered .= $dblist->tt_content_drawItem($record, FALSE);
							array_push($stored[$groupIndex]['areas'][$areaIndex]['records'], $rendered);
							array_push($areas[$area['name']]['records'], $rendered);
						}
					}
				}
				$this->view->assignMultiple($flexform);
				$this->view->assign('areas', $areas);
				$this->view->assign('row', $row);
				$this->view->assignMultiple($stored);
				$this->view->assign('fce', $stored);
				$itemContent = $this->view->render();
				$drawItem = FALSE;
			} catch (Exception $e) {
				#var_dump($e->getMessage());
			}
		}
	}

}


?>
