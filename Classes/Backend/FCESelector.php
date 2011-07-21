<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Class that renders a selection field for Fluid FCE template selection
 *
 * @package	TYPO3
 * @subpackage	fed
 */
class Tx_Fed_Backend_FCESelector {

	public function renderField(&$parameters, &$pObj) {
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$configManager = $objectManager->get('Tx_Extbase_Configuration_BackendConfigurationManager');
		$config = $configManager->getTyposcriptSetup();
		$config = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($config);
		$typoscript = $config['plugin']['tx_fed']['fce'];
		$paths = $typoscript['templatePaths'];
		$gathered = array();
		$gathered['standalone'] = array();
		foreach ($paths as $path) {
			if (strpos($path, 'EXT:') === 0) {
				$slice = strpos($path, '/');
				$extKey = array_pop(explode(':', substr($path, 0, $slice)));
				$path = t3lib_extMgm::siteRelPath($extKey) . substr($path, $slice);
				$gathered[$extKey] = $this->getFiles($path, TRUE);
			} else {
				$gathered['standalone'] = array_merge($gathered['standalone'], $this->getFiles($path, TRUE));
			}
		}
		$name = $parameters['itemFormElName'];
		$value = $parameters['itemFormElValue'];
		$select = "<div><select name='{$name}'  class='formField select' onchange='if (confirm(TBE_EDITOR.labels.onChangeAlert) && TBE_EDITOR.checkSubmit(-1)){ TBE_EDITOR.submitForm() };'>" . chr(10);
		$select .= "<option value=''>(Select Fluid FCE type)</option>" . chr(10);
		foreach ($gathered as $extKey=>$files) {
			if (count($files) > 0) {
				if ($extKey == 'standalone') {
					$label = 'Standalone Templates';
				} else {
					$label = 'EXT:' . $extKey;
				}
				$select .= "<optgroup label='{$label}'>" . chr(10);
				foreach ($files as $fileRelPath) {
					$view = $objectManager->get('Tx_Fed_View_FlexibleContentElementView');
					$view->setTemplatePathAndFilename(PATH_site . $fileRelPath);
					$label = $view->harvest('FEDFCELABEL');
					$enabled = $view->harvest('FEDFCEENABLED');
					if ($enabled !== 'FALSE') {
						if (!$label) {
							$label = $fileRelPath;
						}
						$selected = ($fileRelPath == $value ? " selected='selected'" : "");
						$select .= "<option value='{$fileRelPath}'{$selected}>{$label}</option>" .chr(10);
					}
				}
				$select .= "</optgroup>" . chr(10);
			}
		}
		$select .= "</select></div>" . chr(10);
		return $select;

	}

	protected function getFiles($basePath, $recursive=FALSE) {
		$files = scandir(PATH_site . $basePath);
		$addFiles = array();
		foreach ($files as $index=>$file) {
			if (substr($file, 0, 1) === '.') {
				continue;
			} else if (is_dir(PATH_site . $basePath . $file) && $recursive) {
				foreach ($this->getFiles($basePath . $file . '/', $recursive) as $addFile) {
					$addFiles[] = $addFile;
				}
			} else if (is_file(PATH_site . $basePath . $file)) {
				$addFiles[] = $basePath . $file;
			}
		}
		sort($addFiles);
		return (array) $addFiles;
	}
}
?>