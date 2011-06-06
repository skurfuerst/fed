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
 * Allows advanced access to the DOM <head> content while rendering
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_DocumentHead implements t3lib_Singleton {

	const TYPE_JAVASCRIPT = 'js';
	const TYPE_STYLESHEET = 'css';

	/**
	 * @var Tx_Extbase_Object_Manager
	 */
	protected $objectManager;

	/**
	 * @param Tx_Extbase_Object_Manager $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_Manager $objectManager) {
		$this->objectManager = $objectManager;
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
	 * @param int $index Position to take in additionalHeaderData; pushes current resident DOWN
	 * @api
	 */
	public function includeHeader($code, $type=NULL, $key=NULL, $index=-1) {
		if ($type !== NULL) {
			$code = $this->wrap($code, NULL, $type);
		}
		if ($key === NULL) {
			$key = md5($code);
		}
		if (isset($GLOBALS['TSFE']->additionalHeaderData[$key])) {
			unset($GLOBALS['TSFE']->additionalHeaderData[$key]);
		}
		if ($index >= 0) {
			$current = $GLOBALS['TSFE']->additionalHeaderData;
			$new = array($key => $code);
			if ($index === 0) {
				$merged = array_merge($new, $current);
			} else if ($index < count($current)-1) {
				$after = array_splice($current, $index);
				$merged = array_merge($current, $new, $after);
			} else {
				$merged = array_merge($current, $new);
			}
			$GLOBALS['TSFE']->additionalHeaderData = $merged;
		} else {
			$GLOBALS['TSFE']->additionalHeaderData[$key] = $code;
		}
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
	public function wrap($code=NULL, $file=NULL, $type=NULL) {
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
	public function concatenateFiles(array $files, $saveToFile=FALSE, $extension=self::TYPE_JAVASCRIPT) {
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
	public function saveContentToTempFile($contents, $uniqid, $extension) {
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
	 * @param int $index The position in additionalHeaderData to take; pushes current resident DOWN
	 * @return string The MD5 checksum of files (which is also the additionalHeaderData array key if you $concat = TRUE)
	 * @api
	 */
	public function includeFiles(array $filenames, $cache=FALSE, $concat=FALSE, $compress=FALSE, $index=-1) {
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
				$this->includeHeader($code, $type);
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
	 * @param int $index Position to take in additionalHeaderData; pushes current resident DOWN
	 * @return void
	 * @api
	 */
	public function includeFile($filename, $cache=FALSE, $concat=FALSE, $compress=FALSE, $index=-1) {
		$pathinfo = pathinfo($filename);
		$type = $pathinfo['extension'];
		if ($pathinfo['filename'] === '*') {
			$files = $this->getFilenamesOfType($pathinfo['dirname'], $pathinfo['extension']);
			if ($files) {
				$this->includeFiles($files, $cache, $concat, $compress, $index);
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
		$this->includeHeader($code, NULL, NULL, $index);
	}

	/**
	 * Wrapper for includeFile($filename,$cache,$concat,$compress,$index) for ease.
	 * Use this to include files at the very top of additionalHeaderData even
	 * though the command ran at the very end of processing.
	 *
	 * @param string $filename Name of file to include at index $index
	 * @param string $index Position to hijack (push resident DOWN) in additionalHeaderData. Assumes you want the TOP position ;)
	 * @api
	 */
	public function includeFileAt($filename, $index=0) {
		return $this->includeFile($filename, FALSE, FALSE, FALSE, $index);
	}

	/**
	 * Get an array of all with extension $extension in $dir
	 *
	 * NOTE: this method doesn't really belong here but is required by many
	 * member methods. Stays for now.
	 *
	 * @param string $dir
	 * @param string $type
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