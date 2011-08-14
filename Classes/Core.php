<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010
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
 * FED CORE
 *
 * Quick-access API methods to easily integrate with FED
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 */
abstract class Tx_Fed_Core {

	/**
	 * Contains registered plugin FlexForms
	 * @var array
	 */
	private static $pluginFlexForms = array();

	/**
	 * @var array
	 */
	private static $contentObjectFlexForms = array();

	/**
	 * Registers a Fluid template for use as a Dynamic Flex Form template in the
	 * style of FED's Fluid Content Element and Fluid Page configurations. See
	 * documentation web site for more detailed information about how to
	 * configure such a FlexForm template.
	 *
	 * Note: you can point to your Model Object templates and place the
	 * configuration in these templates - and get automatically transformed
	 * values from your FlexForms, i.e. a Domain Object instance from a "group"
	 * type select box or an ObjectStorage from a list of records. Usual output
	 * is completely ignored, only the "Configuration" section is considered.
	 *
	 * @param string $pluginSignature The plugin signature this FlexForm belongs to
	 * @param string $templateFilename Location of the Fluid template containing field definitions
	 * @param array $variables Optional array of variables to pass to Fluid template
	 */
	public static function registerFluidFlexFormPlugin($pluginSignature, $templateFilename, array $variables=array()) {
		self::$pluginFlexForms[$pluginSignature] = array(
			'pluginSignature' => $pluginSignature,
			'templateFilename' => $templateFilename,
			'variables' => $variables
		);
	}

	/**
	 * Same as registerFluidFlexFormPlugin, but uses a content object type for
	 * resolution - use this if you registered your Extbase plugin as a content
	 * object in your localconf.
	 *
	 * @param string $contentObjectType The cType of the object you registered
	 * @param string $templateFilename Location of the Fluid template containing field definitions
	 * @param array $variables Optional array of variables to pass to Fluid template
	 */
	public static function registerFluidFlexFormContentObject($contentObjectType, $templateFilename, array $variables=array()) {
		self::$contentObjectFlexForms[$contentObjectType] = array(
			'contentObjectType' => $contentObjectType,
			'templateFilename' => $templateFilename,
			'variables' => $variables
		);
	}

	/**
	 * Gets the defined FlexForms based on parameters
	 * @param string $type Optional; The type (plugin, contentObject) of registered FlexForm templates to get
	 * @param string $signature Optional; The plugin signature or cType based on which $type you requested
	 * @return array
	 */
	public static function getRegisteredFlexForms($type=NULL, $signature=NULL) {
		$all = array(
			'pluginFlexForms' => self::$pluginFlexForms,
			'contentObjectFlexForms' => self::$contentObjectFlexForms
		);
		if (!$type && !$signature) {
			return $all;
		} else if ($type && !$signature) {
			return $all[$type . 'FlexForms'];
		} else if ($type && $signature) {
			return $all[$type . 'FlexForms'][$signature];
		}
	}

}

?>