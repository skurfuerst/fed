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
 *
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Resource
 *
 */
class Tx_Fed_ViewHelpers_Resource_FileViewHelper extends Tx_Fed_ViewHelpers_ResourceViewHelper {


	/**
	 * Intialize arguments relevant for file resources
	 */
	public function initializeArguments() {
		// initialization of arguments which relate to array('key' => 'filename')
		// type resource ViewHelpers
		parent::initializeArguments();
		$this->registerArgument('files', 'array', 'Array of files to process', FALSE, NULL);
		$this->registerArgument('sql', 'string', 'SQL Query to fetch files, must return either just "filename" or "uid, filename" field in that order', FALSE, NULL);
	}

	/**
	 * Render / process
	 *
	 * @return string
	 */
	public function render() {
		// if no "as" argument and no child content, return linked list of files
		// else, assign variable "as"
		$pathinfo = pathinfo($this->arguments['path']);
		if (is_dir($pathinfo['dirname']) === FALSE) {
			$pathinfo = pathinfo(PATH_site . $this->arguments['path']);
		}
		if ($pathinfo['filename'] === '*') {
			$files = $this->documentHead->getFilenamesOfType($pathinfo['dirname'], $pathinfo['extension']);
		} else if (is_array($this->arguments['file'])) {
			$files = $this->arguments['files'];
			foreach ($files as $k=>$v) {
				$files[$k] = PATH_site . $v;
			}
		} else if (is_dir($pathinfo['dirname'] . '/' . $pathinfo['basename'])) {
			$files = scandir($pathinfo['dirname'] . '/' . $pathinfo['basename']);
			foreach ($files as $k=>$file) {
				$file = $pathinfo['dirname'] . '/' . $pathinfo['basename'] . '/' . $file;
				if (is_dir($file)) {
					unset($files[$k]);
				} else if (substr($file, 0, 1) === '.') {
					unset($files[$k]);
				} else {
					$files[$k] = $file;
				}
			}
		} else {
			throw new Exception('Invalid path given to Resource ViewHelper', $code, $previous);
		}
		$files = $this->arrayToFileObjects($files);
		$files = $this->sortFiles($files);
		// rendering
		if ($this->arguments['as']) {
			$this->templateVariableContainer->add($this->arguments['as'], $files);
		} else if ($this->arguments['return'] === TRUE) {
			return $files;
		} else {
			$this->templateVariableContainer->add('files', $files);
			$content = $this->renderChildren();
			$this->templateVariableContainer->remove('files');
			// possible return: HTML file list
			if (strlen(trim($content)) === 0) {
				return $this->renderFileList($files);
			} else {
				return $content;
			}
		}
	}

}

?>
