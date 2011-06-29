<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ************************************************************* */

/**
 * ViewHelper used to render the FlexForm definition for Fluid FCEs
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Fce/Field
 */

class Tx_Fed_ViewHelpers_Fce_RenderFieldViewHelper extends Tx_Fed_ViewHelpers_Fce_FieldViewHelper {

	public function initializeArguments() {
		$this->registerArgument('config', 'array', 'Configuration of the field');
	}

	public function render() {
		$config = $this->arguments['config'];
		return $this->getCustomizedConfiguration($config);
	}

	protected function getWizardConfiguration($config) {
		if (count($config['wizards']) == 0) {
			return NULL;
		}
		$xml = "<wizards type='array'>" . chr(10);
		foreach ($config['wizards'] as $name=>$wizard) {
			$xml .= "<{$name} type='array'>";
			if (is_array($wizard) === FALSE) {
				$xml .= $wizard;
			} else {
				$xml .= chr(10);
				foreach ($wizard as $fieldName=>$fieldValue) {
					$xml .= "<{$fieldName}>{$fieldValue}</{$fieldName}>" . chr(10);
				}
				$xml .= chr(10);
			}
			$xml .= "</{$name}>" .chr(10);
		}
		$xml .= "</wizards>" . chr(10);
		return $xml;
	}

	protected function getCustomizedConfiguration($config) {
		$type = $config['type'];
		$method = "get" . ucfirst($config['type']) . "Configuration";
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), array($config));
		} else {
			throw new Exception('Unsupported field type in Fluid FCE: ' . $config['type']);
		}
	}

	protected function getInputConfiguration($config) {
		$wizards = $this->getWizardConfiguration($config);
		$xml = <<< XML
<label>{$config['label']}</label>
<required>{$config['required']}</required>
<config>
	<type>{$config['type']}</type>
	<default>{$config['default']}</default>
	<eval>{$config['eval']}</eval>
	{$wizards}
</config>
XML;
		return $xml;
	}

	protected function getSelectConfiguration($config, $addedConfig=NULL) {
		$wizards = $this->getWizardConfiguration($config);
		if ($config['items']) {
			$switchedConfig = '<items type="array">' . chr(10);
			foreach ($config['items'] as $iteration=>$set) {
				if (count($set) == 1) {
					$set[1] = $set[0]; // option value becomes label
				}
				$switchedConfig .= '<numIndex type="array" index="' . $iteration . '">' . chr(10);
				$switchedConfig .= '	<numIndex index="0">' . $set[1] . '</numIndex>' . chr(10);
				$switchedConfig .= '	<numIndex index="1">' . $set[0] . '</numIndex>' . chr(10);
				$switchedConfig .= '</numIndex>' . chr(10);
			}
			$switchedConfig .= '</items>' . chr(10);
		} else if ($config['table']) {
			$switchedConfig = implode(chr(10), array(
				"<foreign_table>{$config['table']}</foreign_table>",
				"<foreign_table_where>{$config['condition']}</foreign_table_where>",
				"<MM>{$config['mm']}</MM>",
			));
		}
		$xml = <<< XML
<label>{$config['label']}</label>
<required>{$config['required']}</required>
<config>
	<type>{$config['type']}</type>
	<minitems>{$config['minItems']}</minitems>
	<maxitems>{$config['maxItems']}</maxitems>
	<size>{$config['size']}</size>
	<multiple>{$config['multiple']}</multiple>
	<show_thumbs>{$config['showThumbs']}</show_thumbs>
	<itemsProcFunc>{$config['itemsProcFunc']}</itemsProcFunc>
	{$switchedConfig}
	{$addedConfig}
	{$wizards}
</config>
XML;
		return $xml;
	}

	protected function getTextConfiguration($config) {
		$wizards = $this->getWizardConfiguration($config);
		$xml = <<< XML
<label>{$config['label']}</label>
<required>{$config['required']}</required>
<config>
	<type>{$config['type']}</type>
	<default>{$config['default']}</default>
	<cols>{$config['cols']}</cols>
	<rows>{$config['rows']}</rows>
	{$wizards}
</config>
<defaultExtras>{$config['defaultExtras']}</defaultExtras>
XML;
		return $xml;
	}

	protected function getCheckConfiguration($config) {
		$wizards = $this->getWizardConfiguration($config);
		$xml = <<< XML
<label>{$config['label']}</label>
<required>{$config['required']}</required>
<config>
	<type>{$config['type']}</type>
	{$wizards}
</config>
XML;
		return $xml;
	}

	protected function getGroupConfiguration($config) {
		$added = <<< XML
	<allowed>{$config['allowed']}</allowed>
	<internal_type>{$config['internalType']}</internal_type>
XML;
		$xml = $this->getSelectConfiguration($config, $added);
	#header("content-type: text/plain");
	#die($xml);
		return $xml;
	}

	protected function getUserConfiguration($config) {
		$wizards = $this->getWizardConfiguration($config);
		$xml = <<< XML
<label>{$config['label']}</label>
<required>{$config['required']}</required>
<config>
	<type>{$config['type']}</type>
	<userFunc>{$config['userFunc']}</userFunc>
	{$wizards}
</config>
XML;
		return $xml;
	}

}

?>