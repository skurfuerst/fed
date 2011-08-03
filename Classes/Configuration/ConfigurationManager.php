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
 * Configuration Manager subclass. Contains additional configuration fetching
 * methods used in FED's features.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Configuration
 */
class Tx_Fed_Configuration_ConfigurationManager extends Tx_Extbase_Configuration_ConfigurationManager implements Tx_Extbase_Configuration_ConfigurationManagerInterface {

	/**
	 * Get definitions of paths for FCEs defined in TypoScript
	 *
	 * @param string $extensionName Optional extension name to get only that extension
	 * @return array
	 */
	public function getContentConfiguration($extensionName=NULL) {
		$typoscript = $this->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$paths = $typoscript['plugin.']['tx_fed.']['fce.'];
		$paths = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($paths);
		if ($extensionName) {
			return $paths[$extensionName];
		} else {
			return $paths;
		}
	}

	/**
	 * Get definitions of paths for Page Templates defined in TypoScript
	 *
	 * @param string $extensionName
	 * @return array
	 */
	public function getPageConfiguration($extensionName=NULL) {
		$config = $this->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$config = $config['plugin.']['tx_fed.']['page.'];
		$config = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($config);
		return $config;
	}

}

?>