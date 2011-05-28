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
 * Loads required resources for jQuery and jQueryUI usage. Gets the source
 * files from Google CDN to improve performance and chance of cache hits - plus
 * adds parallel loading to generally decrease page-ready waiting time. Good-
 *
 * Can be called multiple times but only the first instance encountered is
 * respected - this to avoid version collissions.
 *
 * You should NOT use this if you are using t3jquery to always load jQuery!
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\JQuery
 * @uses jQuery
 */
class Tx_Fed_ViewHelpers_JQuery_LoadViewHelper extends Tx_Fed_Core_ViewHelper_AbstractJQueryViewHelper {

	protected $tagName = 'div';

	/**
	 * Initialize the arguments used in this ViewHelper
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('jQueryVersion', 'string', 'Which version of jQuery to load. If not specified the newest version is loaded - you should however ALWAYS specify the major and minor version numbers in production mode!');
		$this->registerArgument('jQueryUIVersion' , 'string', 'Which version of jQuery UI to load. If not specified does not load jQueryUI. If value is empty or "current", the newest version is automatically loaded. In production mode you should ALWAYS specifiy the major and minor versions!');
		$this->registerArgument('jQueryUITheme', 'string', 'Name of optional jQueryUI theme to load. Requires use of jQueryUIVersion - selects the CDN theme matching that version number');
		$this->registerArgument('jQueryUIThemeUrl', 'string', 'If used, loads the theme from a specific url');
		$this->registerArgument('compatibility', 'boolean', 'If TRUE, puts jQuery and jQueryUI into compatibility mode');
		$this->registerArgument('report', 'boolean', 'If TRUE, outputs a report about the version of loaded libraries. Use this when you go from development to production and change the version attributes to match those versions.');
	}

	/**
	 * Loads necessary resources from Google CDN
	 */
	public function render() {
		
	}

}

?>
