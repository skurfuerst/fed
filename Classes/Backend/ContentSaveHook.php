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
 * @subpackage Core/ViewHelper
 */

class Tx_Fed_Backend_ContentSaveHook {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var Tx_Fed_Domain_Repository_FceRepository
	 */
	protected $fceRepository;

	/**
	 * @var Tx_Fed_Backend_FCEParser
	 */
	protected $fceParser;

	/**
	 *
	 * @var Tx_Extbase_Service_FlexFormService
	 */
	protected $flexFormService;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->fceRepository = $this->objectManager->get('Tx_Fed_Domain_Repository_FceRepository');
		$this->fceParser = $this->objectManager->get('Tx_Fed_Backend_FCEParser');
		$this->flexFormService = $this->objectManager->get('Tx_Extbase_Service_FlexFormService');
	}

	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$thisObject) {

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_fed_fceuid', $table, "uid = '{$id}'");
		$content = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (is_array($content)) {
			$uid = array_pop($content);
			if ($uid < 1) {
				return;
			}
			$flexFormContent = $fieldArray['pi_flexform'];
			$languagePointer = 'lDEF';
			$valuePointer = 'vDEF';
			$values = $this->flexFormService->convertFlexFormContentToArray($flexFormContent, $languagePointer, $valuePointer);
			if (is_array($values['tt_content'])) {
				foreach ($values['tt_content'] as $name=>$value) {
					$fieldArray[$name] = $value;
				}
			}
			#var_dump($fieldArray);
			#exit();
			#var_dump($fieldArray);
			#var_dump($fieldArray);
			#die();
			#$fce = $this->fceRepository->findByUid($uid);
			#$templateFile = $fce->getFilename();
			#$config = $this->fceParser->getFceDefinitionFromTemplate(PATH_site . $templateFile);

			#$flexformTemplateFile = t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/FlexibleContentElement/AutoFlexForm.xml');
			#$template = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
			#$template->setTemplatePathAndFilename($flexformTemplateFile);
			#$template->assign('fce', $config);
			#$flexformXml = $template->render();
			#$array = t3lib_div::xml2array($flexformXml);
			#header("Content-type: text/plain");
			#var_dump($fieldArray);
			#exit();
			#$fieldArray['pi_flexform'] = $flexformXml;
		}


	}
}

?>