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
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\Extbase
 */
class Tx_Fed_ViewHelpers_Extbase_ApiViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	private $cache = FALSE;
	private $compress = FALSE;
	private $concat = TRUE;
	private $obfuscate = FALSE;
	private $cacheTTL = 0;
	private $domain = FALSE;
	private $extension = NULL;

	protected $type = 'js';

	/**
	 * Includes all JS API name spaces for the domain $domain
	 *
	 * @param string $extension If specified, APIs are read from the default location in this extension
	 * @param string $domain The domain path of the API to load. Defaults to dk.wildside; if specified along with $extension, only $domain's namespace will be loaded
	 * @param boolean $cache If TRUE, the file is cached (works best if you also use one of the compress, concat or obfuscate options
	 * @param boolean $compress If TRUE, the file is compressed
	 * @param boolean $concat If TRUE, the files to include are concatenated
	 * @param boolean $obfuscate If TRUE, code-obfuscation is applied
	 * @param int $cacheTTL How long the cached result file should be cached. Default is 24H (86400)
	 * @return string
	 */
	public function render($extension=NULL, $domain=NULL, $cache=FALSE, $compress=FALSE, $concat=TRUE, $obfuscate=FALSE, $cacheTTL=86400) {
		if ($extension === NULL) {
			$extension = $this->getExtensionName();
		}

		$concat = FALSE;

		$this->extension = $extension;
		$this->domain = $domain;
		$this->cache = $cache;
		$this->compress = $compress;
		$this->concat = $concat;
		$this->obfuscate = $obfuscate;
		$this->cacheTTL = $cacheTTL;
		$files = $this->detectNamespaceFiles();

		$commonCSSfile = t3lib_extMgm::siteRelPath($this->extension) . 'Resources/Public/Stylesheet/Common.css';
		if (file_exists($commonCSSfile)) {
			$this->includeFile($commonCSSfile);
		}

		return $this->renderChildren();
	}

	/**
	 * @return string
	 */
	public function getExtensionName() {
		return 'fed';
	}

	/**
	 * Detect all namespace
	 * @eturn array Files which were included/detected
	 */
	private function detectNamespaceFiles() {
		$jsBasePath = t3lib_extMgm::siteRelPath($this->extension) . 'Resources/Public/Javascript/';
		$files = scandir($jsBasePath);
		foreach ($files as $k=>$file) {
			$pathinfo = pathinfo($jsBasePath.$file);
			if (is_dir($jsBasePath) == FALSE || $pathinfo['extension'] != $this->type) {
				unset($files[$k]);
			} else {
				$this->includes($pathinfo);
			}
		}
		return $files;
	}

	/**
	 * Process include files for API
	 * @return boolean
	 */
	private function includes($pathinfo) {
		$jsBasePath = $pathinfo['dirname'] . '/';
		$namespace = $pathinfo['filename'];
		$namespaceFile = $jsBasePath . $namespace . '.' . $pathinfo['extension'];
		$splitNamespace = $parts = explode('.', $namespace);
		$namespacePath = implode('/', $splitNamespace);
		$contents = file_get_contents(PATH_site . $namespaceFile);
		$lines = explode("\n", $contents);
		$files = array("{$namespaceFile}");
		foreach ($lines as $file) {
			// look for coment-out plus one space - identifies a required file local to the JS namespace
			if (substr($file, 0, 3) == '// ') {
				$file = trim(str_replace('//', '', $file));
				$file = "{$jsBasePath}{$namespacePath}/{$file}";
				array_push($files, $file);
			}
		}
		if ($this->concat === TRUE) {
			$this->includeFiles($files, $this->cache, $this->concat, $this->compress);
		} else {
			foreach ($files as $file) {
				$this->includeFile($file, $this->cache, $this->compress);
			}
		}
		// ad-hoc CSS
		$css = <<< CSS
.fed-json { display: none; }
CSS;
		$this->includeHeader($css, 'css');
		return TRUE;
	}
}






?>