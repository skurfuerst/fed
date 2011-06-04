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
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Core\ViewHelper
 */
abstract class Tx_Fed_Core_ViewHelper_AbstractViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	const TYPE_JAVASCRIPT = 'js';
	const TYPE_STYLESHEET = 'css';

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 * Inject JSON Service
	 * @param Tx_Fed_Utility_JSON $service
	 */
	public function injectJSONService(Tx_Fed_Utility_JSON $service) {
		$this->jsonService = $service;
	}

	/**
	 * Get a StandaloneView for $templateFile
	 *
	 * @param string $templateFile
	 * @return Tx_Fluid_View_StandaloneView
	 * @api
	 */
	public function getTemplate($templateFile) {
		$template = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
		$template->setTemplatePathAndFilename($templateFile);
		return $template;
	}

	/**
	 * Injects $code in header data
	 *
	 * @param string $code A rendered tag suitable for <head>
	 * @param string $type Optional, if left out we assume the code is already wrapped
	 * @param string $key Optional key for referencing later through $GLOBALS['TSFE']->additionalHeaderData, defaults to md5 cheksum of tag
	 * @api
	 */
	public function includeHeader($code, $type=NULL, $key=NULL) {
		if ($type !== NULL) {
			$code = $this->wrap($code, NULL, $type);
		}
		if ($key === NULL) {
			$key = md5($code);
		}
		if (isset($GLOBALS['TSFE']->additionalHeaderData[$key])) {
			unset($GLOBALS['TSFE']->additionalHeaderData[$key]);
		}
		$GLOBALS['TSFE']->additionalHeaderData[$key] = $code;
	}

	/**
	 * Wrap the code in proper HTML tags. Supports CSS and Javascript only - or returns input $code
	 *
	 * @param string $code The code, JS or CSS, to be wrapped
	 * @param string $filename If specified, file is used instead of source inject
	 * @param string $type Type of wrapping (css/js)
	 * @return string
	 * @api
	 */
	protected function wrap($code=NULL, $file=NULL, $type=NULL) {
		/**
		 * Type-support for old js/css inject ViewHelpers - will be removed along with those two
		 * @deprecated
		 */
		if ($type === NULL) {
			$type = $this->type;
		}
		if ($type == self::TYPE_JAVASCRIPT) {
			if ($file) {
				return "<script type='text/javascript' src='{$file}'></script>";
			} else {
				return "<script type='text/javascript'>\n{$code}\n</script>";
			}
		} else if ($type == self::TYPE_STYLESHEET) {
			if ($file) {
				return "<link rel='stylesheet' type='text/css' href='{$file}' />";
			} else {
				return "<style type='text/css'>\n{$code}\n</style>";
			}
		} else {
			return $code;
		}
	}

	/**
	 * Concatenate files into a string. You can use this in your subclassed extensions to
	 * combine css/js files in sequence to generate a single output file or code chunk
	 *
	 * @param array $files
	 * @param boolean $saveToFile
	 * @return string Contents or filename if $saveToFile was specified
	 * @api
	 */
	protected function concatenate(array $files, $saveToFile=FALSE, $extension=self::TYPE_JAVASCRIPT) {
		$contents = "";
		foreach ($files as $file) {
			$contents .= file_get_contents(PATH_site . $file);
			$contents .= "\n";
		}
		if ($saveToFile) {
			$md5 = md5(implode('', $files));
			$contents = $this->saveToTempFile($contents, $md5, $extension);
		}
		return $contents;
	}

	/**
	 * Save $contents to a temporary file, for example a combined .css file
	 *
	 * @param string $contents Contents of the file
	 * @param string $uniqid Unique id of the temporary file
	 * @param string $extension Extensin of the filename
	 * @api
	 */
	protected function saveToTempFile($contents, $uniqid, $extension) {
		$tempFilePath = "typo3temp/{$uniqid}.{$extension}";
		$tempFile = PATH_site . $tempFilePath;
		file_put_contents($tempFile, $contents);
		return $tempFilePath;
	}

	/**
	 * Include a list of files with optional concat, compress and cache
	 *
	 * @param array $filenames Filenames to include
	 * @param boolean $cache If true, the file is cached (makes sens if $concat or one of the other options is specified)
	 * @param boolean $concat If true, files are concatenated
	 * @param boolean $compress If true, files are compressed
	 * @return string The MD5 checksum of files (which is also the additionalHeaderData array key if you $concat = TRUE)
	 * @api
	 */
	public function includeFiles(array $filenames, $cache=FALSE, $concat=FALSE, $compress=FALSE) {
		$pathinfo = pathinfo($filename);
		$type = $pathinfo['extension'];
		if ($type !== 'css' && $type !== 'js') {
			$type = 'js'; // assume Javascript for unknown files - this may change later on...
		}
		if ($concat === TRUE) {
			$file = $this->concatenate($filenames, $cache, $type);
			if ($cache === TRUE) {
				$this->includeFile($file, $cache, $compress);
			} else {
				$code = $this->wrap(NULL, $file); // will be added as header code
				$this->process($code);
			}
		} else {
			foreach ($filenames as $file) {
				$this->includeFile($file, $cache, $compress);
			}
		}
	}

	/**
	 * Include a single file with optional concat, compress and cache
	 *
	 * @param array $filenames Filenames to include
	 * @param boolean $cache If true, the file is cached (makes sens if $concat or one of the other options is specified)
	 * @param boolean $concata If true and wildcard filename used, concats all files
	 * @param boolean $compress If true, files are compressed
	 * @return void
	 * @api
	 */
	public function includeFile($filename, $cache=FALSE, $concat=FALSE, $compress=FALSE) {
		$pathinfo = pathinfo($filename);
		$type = $pathinfo['extension'];
		if ($pathinfo['filename'] === '*') {
			$files = $this->getFilenamesOfType($pathinfo['dirname'], $pathinfo['extension']);
			if ($files) {
				$this->includeFiles($files, $cache, $concat, $compress);
			}
			return;
		}
		if ($type !== 'css' && $type !== 'js') {
			$type = 'js'; // assume Javascript for unknown files - this may change later on...
		}
		if ($cache === FALSE && $compress === FALSE) {
			$code = $this->wrap(NULL, $filename, $type);
		} else if ($compress === TRUE) {
			$contents = file_get_contents(PATH_site . $filename);
			$packed = $this->pack($contents);
			$md5 = md5($filename);
			if ($cache === TRUE) {
				$cachedFile = $this->saveToTempFile($contents, $uniqid, $type);
				$code = $this->wrap(NULL, $cachedFile, $type);
			} else {
				$code = $this->wrap($contents, NULL, $type);
			}
		} else {
			$code = $this->wrap(NULL, $filename, $type);
		}
		$this->includeHeader($code);
	}

	/**
	 * Get an array of all with extension $extension in $dir
	 * @param string $dir
	 * @param string $type
	 * @api
	 */
	public function getFilenamesOfType($dir, $extension=NULL) {
		$relative = $dir;
		if (substr($dir, 0, 1) != '/') {
			$dir = PATH_site . $dir;
		}
		$files = scandir($dir);
		foreach ($files as $k=>$file) {
			$pathinfo = pathinfo($dir.$file);
			if (is_dir($dir.$file)) {
				unset($files[$k]);
			} else if ($extension && $pathinfo['extension'] != $extension) {
				unset($files[$k]);
			} else {
				$files[$k] = "{$relative}/{$file}";
			}
		}
		sort($files);
		return $files;
	}

	/**
	 * Pack/compress Javascript code
	 * @param string $code
	 * @api
	 */
	public function pack($code) {
		$encoding = 62; // see value in Tx_Fed_Utility_JavascriptPacker
		$fastDecode = FALSE;
		$specialChars = FALSE;
		$packer = $this->objectManager->get('Tx_Fed_Utility_JavascriptPacker', $encoding, $fastDecode, $specialChars);
		$packed = $packer->pack();
		return (string) $packed;
	}

}

?>