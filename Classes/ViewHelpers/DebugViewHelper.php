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
 * Master debug ViewHelper
 *
 * USE THIS ONLY IN DEVELOPMENT!
 *
 * What this does is:
 *
 * - renders graphs and overview covering debugging subsections all the way from
 *   the controller through all template rendering.
 * - enables automatic template variable output for ALL ViewHelpers which subclass
 *   AbstractDebugViewHelper - when done, simply subclass FED's AbstractViewHelper
 *   instead and debugging is disabled and performance improved.
 * - enables automatic profiling of ALL ViewHelpers which subclass
 *   AbstractDebugViewHelper. Outputs the profile at the very end of execution
 * - enables access to controls over cache, cookies and session variables
 * - allows you to (TEMPORARILY OF COURSE!) disable the cache for the page
 *   executing the Fluid template.
 * - Finally, dumps an overview of the rendered stack at the end of execution.
 *
 * USAGE:
 *
 * <fed:debug>
 * <!-- debugging is now ENABLED with all default options. -->
 *
 * <!-- any ViewHelpers subclassing AbstractDebugViewHelper gets profiled and
 *      has its current variables added to its output. -->
 *
 * <!-- at the end, just before the closing debug tag, profile and stack gets
 *      rendered -->
 *
 * </fed:debug>
 *
 * You may also use the singleton Tx_Fed_Utility_Debug to initiate debugging.
 * In addition, this Service lets you profile your Extbase code as you please.
 * If you choose this method simply add <fed:debug /> at the last piece of
 * Fluid template that gets rendered - usually this would be at the bottom of
 * your Layout file.
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers
 *
 */
class Tx_Fed_ViewHelpers_DebugViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Fed_Utility_Debug
	 */
	protected $debugService;

	/**
	 * @param Tx_Fed_Utility_Debug $debugService
	 */
	public function injectDebugService(Tx_Fed_Utility_Debug $debugService) {
		$this->debugService = $debugService;
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('cacheControl', 'boolean', 'If TRUE, adds cache control features', FALSE, TRUE);
		$this->registerArgument('sessionControl', 'boolean', 'If TRUE, adds session control features', FALSE, TRUE);
		$this->registerArgument('cookieControl', 'boolean', 'If TRUE, adds cookie control features', FALSE, TRUE);
		$this->registerArgument('noCache', 'boolean', 'If TRUE, disables caching for the current page (by clearing it on every run). Also prevents Extbase reflection caching', FALSE, FALSE);
		$this->registerArgument('stack', 'boolean', 'If TRUE, renders a Stack Dump to see your rendering order', FALSE, TRUE);
		$this->registerArgument('variables', 'boolean', 'If TRUE, renders an overview of all registered Fluid template variables', FALSE, TRUE);
		$this->registerArgument('profile', 'boolean', 'If TRUE, automatically profiles all renderChildren() methods of all AbstractDebugViewHelper subclasses - USE WITH CARE!', FALSE, FALSE);
	}

	/**
	 * Renders a nostalgic "Under Construction" with a modern twist
	 *
	 * @return string
	 */
	public function render() {
		try {
			$content = $this->renderChildren();
		} catch (Exception $exception) {
			$content = $this->renderOnException($exception);
		}
		$content .= $this->renderDebugInfo();
		return $content;
	}

	/**
	 * Renders the configured debug information to a string
	 *
	 * @return string
	 */
	protected function renderDebugInfo() {
		$content = "";
		return $content;
	}

	/**
	 * Render a message for the developer if an Exception was thrown
	 *
	 * @param Exception $exception
	 * @return string
	 */
	protected function renderOnException(Exception $exception) {
		$content = "";
		return $content;
	}

}

?>
