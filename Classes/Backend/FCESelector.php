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
		$configManager = t3lib_div::makeInstance('Tx_Extbase_Configuration_BackendConfigurationManager');
		$config = $configManager->getTyposcriptSetup();
		$config = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($config);
		$typoscript = $config['plugin']['tx_fed']['fce'];
		$files = $this->getFiles($typoscript['templatePath'], $typoscript['recursive'] == '0');
		$name = $parameters['itemFormElName'];
		$value = $parameters['itemFormElValue'];
		$select = "<select name='{$name}'>" . chr(10);
		foreach ($files as $fileRelPath) {
			$selected = ($fileRelPath == $value ? " selected='selected'" : "");
			$select .= "<option value='{$fileRelPath}'{$selected}>{$fileRelPath}</option>" .chr(10);
		}
		$select .= "</select>" . chr(10);
		return $select;

	}

	protected function getFiles($basePath, $recursive=FALSE) {
		$files = scandir(PATH_site . $basePath);
		$addFiles = array();
		foreach ($files as $index=>$file) {
			if (substr($file, 0, 1) == '.') {
				continue;
			} else if (is_dir(PATH_site . $basePath . $file) && $recursive) {
				$subFiles = $this->getFiles($basePath . $file . '/', $recursive);
				$addFiles = array_merge($addFiles, $subFiles);
			} else if (is_file(PATH_site . $basePath . $file)) {
				$addFiles[$index] = $basePath . $file;
			}
		}
		return (array) $addFiles;
	}
}
?>