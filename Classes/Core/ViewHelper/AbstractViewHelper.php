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
 * Provides injected services and methods for easier implementation in
 * subclassing ViewHelpers
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Core\ViewHelper
 */
abstract class Tx_Fed_Core_ViewHelper_AbstractViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	/**
	 * Inject JSON Service
	 * @param Tx_Fed_Utility_JSON $service
	 */
	public function injectJSONService(Tx_Fed_Utility_JSON $service) {
		$this->jsonService = $service;
	}

	/**
	 * @param Tx_Fed_Utility_DocumentHead $documentHead
	 */
	public function injectDocumentHead(Tx_Fed_Utility_DocumentHead $documentHead) {
		$this->documentHead = $documentHead;
	}

	/**
	 * Get a StandaloneView for $templateFile
	 * DEPRECATED: moved to Tx_Fed_Utility_DocumentHead which is injected at
	 * $this->documentHead
	 *
	 * @param string $templateFile
	 * @return Tx_Fluid_View_StandaloneView
	 * @deprecated
	 */
	public function getTemplate($templateFile) {
		return $this->documentHead->getTemplate($templateFile);
	}

	/**
	 * Injects $code in header data
	 * DEPRECATED: moved to Tx_Fed_Utility_DocumentHead which is injected at
	 * $this->documentHead
	 *
	 * @param string $code A rendered tag suitable for <head>
	 * @param string $type Optional, if left out we assume the code is already wrapped
	 * @param string $key Optional key for referencing later through $GLOBALS['TSFE']->additionalHeaderData, defaults to md5 cheksum of tag
	 * @deprecated
	 */
	public function includeHeader($code, $type=NULL, $key=NULL) {
		return $this->documentHead->includeHeader($code, $type, $key);
	}

	/**
	 * Wrap the code in proper HTML tags. Supports CSS and Javascript only - or returns input $code
	 * DEPRECATED: moved to Tx_Fed_Utility_DocumentHead which is injected at
	 * $this->documentHead
	 *
	 * @param string $code The code, JS or CSS, to be wrapped
	 * @param string $filename If specified, file is used instead of source inject
	 * @param string $type Type of wrapping (css/js)
	 * @return string
	 * @deprecated
	 */
	protected function wrap($code=NULL, $file=NULL, $type=NULL) {
		if ($type === NULL) {
			$type = $this->type;
		}
		return $this->documentHead->wrap($code, $file, $type);
	}

	/**
	 * Concatenate files into a string. You can use this in your subclassed extensions to
	 * combine css/js files in sequence to generate a single output file or code chunk
	 * DEPRECATED: moved to Tx_Fed_Utility_DocumentHead which is injected at
	 * $this->documentHead
	 *
	 * @param array $files
	 * @param boolean $saveToFile
	 * @return string Contents or filename if $saveToFile was specified
	 * @deprecated
	 */
	protected function concatenate(array $files, $saveToFile=FALSE, $extension=Tx_Fed_Utility_DocumentHead::TYPE_JAVASCRIPT) {
		return $this->documentHead->concatenateFiles($files, $saveToFile, $extension);
	}

	/**
	 * Save $contents to a temporary file, for example a combined .css file
	 * DEPRECATED: moved to Tx_Fed_Utility_DocumentHead which is injected at
	 * $this->documentHead
	 *
	 * @param string $contents Contents of the file
	 * @param string $uniqid Unique id of the temporary file
	 * @param string $extension Extensin of the filename
	 * @deprecated
	 */
	protected function saveToTempFile($contents, $uniqid, $extension) {
		return $this->documentHead->saveContentToTempFile($contents, $uniqid, $extension);
	}

	/**
	 * Include a list of files with optional concat, compress and cache
	 * May be deprecated in the future but since the function is so vital, it
	 * will remain proxied to DocumentHead for now - However, it has been
	 * demoted from @api to non-api
	 *
	 * @param array $filenames Filenames to include
	 * @param boolean $cache If true, the file is cached (makes sens if $concat or one of the other options is specified)
	 * @param boolean $concat If true, files are concatenated
	 * @param boolean $compress If true, files are compressed
	 * @return string The MD5 checksum of files (which is also the additionalHeaderData array key if you $concat = TRUE)
	 */
	public function includeFiles(array $filenames, $cache=FALSE, $concat=FALSE, $compress=FALSE) {
		return $this->documentHead->includeFiles($filenames, $cache, $concat, $compress);
	}

	/**
	 * Include a single file with optional concat, compress and cache
	 * Demoted from @api to non-api but remains as proxy into DocumentHead
	 *
	 * @param array $filenames Filenames to include
	 * @param boolean $cache If true, the file is cached (makes sens if $concat or one of the other options is specified)
	 * @param boolean $concata If true and wildcard filename used, concats all files
	 * @param boolean $compress If true, files are compressed
	 * @return void
	 */
	public function includeFile($filename, $cache=FALSE, $concat=FALSE, $compress=FALSE) {
		return $this->documentHead->includeFile($filename, $cache, $concat, $compress);
	}

	/**
	 * Get an array of all with extension $extension in $dir
	 *
	 * Remains as @api method but proxies to DocumentHead. Final location of
	 * getFilenamesOfType($dir, $extension) may change but this method will remain.
	 *
	 * @param string $dir
	 * @param string $type
	 * @api
	 */
	public function getFilenamesOfType($dir, $extension=NULL) {
		return $this->documentHead->getFilenamesOfType($dir, $extension);
	}

	/**
	 * Pack/compress Javascript code. Demoted from @api to non-api, fuction
	 * moved to Tx_Fed_Utility_DocumentHead
	 * @param string $code
	 */
	public function pack($code) {
		return (string) $this->documentHead->pack($code);
	}

}

?>