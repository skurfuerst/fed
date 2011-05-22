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
 * Controller 
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Controller
 */
class Tx_Fed_Controller_ToolController extends Tx_Fed_Core_AbstractController {

	/**
	 * @param string $target Keyword target of the cache(s) to clear (all, extbase, page, config)
	 * @return string
	 * @dontverifyrequesthash
	 */
	public function clearCacheAction($target) {
		if ($target === 'page') {
			$this->clearPageCache();
		} else if ($target === 'extbase') {
			$this->clearExtbaseCache();
		} else if ($target === 'pages') {
			$this->clearAllCaches();
		} else if ($target === 'files') {
			$this->clearCachedFiles();
		}
		return '1';
	}
	
	/**
	 * @param string $target Dotted target path of session variable to inspect
	 * @return string
	 * @dontverifyrequesthash
	 */
	public function inspectSessionAction($target) {
		return $target;
	}
	
	/**
	 * @param string $target Name of cookie to inspect
	 * @return string
	 * @dontverifyrequesthash
	 */
	public function inspectCookieAction($target) {
		return $_COOKIE[$target];
	}
	
	/**
	 * 
	 * @param string $target
	 * @param string $value
	 */
	public function setCookieAction($target, $value) {
		$_COOKIE[$target] = $value;
		return '1';
	}
	
	/**
	 * 
	 * @param string $target
	 */
	public function removeCookieAction($target) {
		setcookie($target, '', time()-1);
		return '1';
	}
	
	
	protected function clearCachedFiles() {
		$mask = "temp_CACHED_";
		$folder = new DirectoryIterator(PATH_site . "typo3conf/");
		foreach ($folder as $file) {
			$name = $file->getFileName();
			if (substr($name, 0, strlen($mask)) === $mask) {
				unlink(PATH_site . "typo3conf/" . $name);
			}
		}
	}
	
	protected function clearPageCache($pages=NULL) {
		if ($pages === NULL) {
			$pages = array($GLOBALS['TSFE']->id);
		}
		$this->cacheService->clearPageCache($pages);
	}
	
	protected function clearAllPageCaches() {
		$pages = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'pages');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			array_push($pages, $row['uid']);
		}
		$this->clearPageCache($pages);
	}
	
	protected function clearExtbaseCache() {
		$GLOBALS['TYPO3_DB']->exec_TRUNCATEquery('tx_extbase_cache_object');
		$GLOBALS['TYPO3_DB']->exec_TRUNCATEquery('tx_extbase_cache_object_tags');
		$GLOBALS['TYPO3_DB']->exec_TRUNCATEquery('tx_extbase_cache_reflection');
		$GLOBALS['TYPO3_DB']->exec_TRUNCATEquery('tx_extbase_cache_reflection_tags');
		$this->cacheService->clearPageCache(array($GLOBALS['TSFE']->id));
	}
	
	protected function clearAllCaches() {
		$this->clearExtbaseCache();
		$this->clearAllPageCaches();
		$this->clearCachedFiles();
	}
	
}

?>