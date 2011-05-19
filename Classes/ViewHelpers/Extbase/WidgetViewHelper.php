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
 * Creates a Widget container
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\Extbase
 */
class Tx_Fed_ViewHelpers_Extbase_WidgetViewHelper extends Tx_Fed_Core_ViewHelper_AbstractExtbaseViewHelper {
	
	/**
	 * Register arguments for this ViewHelper
	 * 
	 * @author Claus Due, Wildside A/S
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('object', 'Tx_Extbase_DomainObject_AbstractEntity', 'Optional object to bind to this AJAX Widget', FALSE);
	}
	
	/**
	 * @author Claus Due, Wildside A/S
	 * @return string
	 */
	public function render() {
		$this->setAction($this->arguments['action']);
		if ($this->hasArgument('object')) {
			$this->setObject($this->arguments['object']);
		}
		if ($this->hasArgument('controllerName')) {
			$this->setControllerName($this->arguments['controllerName']);
		}
		if ($this->hasArgument('extensionName')) {
			$this->setExtensionName($this->arguments['extensionName']);
		}
		if ($this->hasArgument('pluginName')) {
			$this->setPluginName($this->arguments['pluginName']);
		}
		return $this->renderChildren();
	}
	
	/**
	 * Render tag content
	 * 
	 * @author Claus Due, Wildside A/S
	 * @return string
	 */
	public function renderChildren() {
		$this->setAction($this->arguments['action']);
		if ($this->arguments->hasArgument('object')) {
			$this->setObject($this->arguments['object']);
			$data = $this->propertyMapper->getValuesByAnnotation($data, 'json', TRUE);
		} else {
			$this->setControllerName($this->arguments['controllerName']);
			$this->setExtensionName($this->arguments['extensionName']);
			$data = new stdClass();
		}
		$json = $this->jsonService->encode(array_merge($this->arguments['config'], array(
			'api' => "?type={$this->arguments['typeNum']}",
			'displayType' => $this->arguments->hasArgument('displayType') ? $this->arguments['displayType'] : $this->getDisplayType(),
			'controller' => $this->getControllerName(),
			'page' => $this->arguments['pageUid'],
			'action' => $this->getAction(),
			'name' => $this->arguments['name'],
			'plugin' => $this->getRequestPrefix(),
			'data' => $data
		)));
		$html = parent::renderChildren();
		$html = "<div class='fed-widget {$class}'>
			<div class='fed-json'>{$json}</div>
			{$html}
		</div>";
		return $html;
	}
	
	/**
	 * 
	 * @author Claus Due, Wildside A/S
	 * @param string $templateFile
	 * @param string $default
	 * return Tx_Fluid_View_StandaloneView
	 */
	public function getTemplate($templateFile, $default='Widget/Widget.html') {
		if ($templateFile === NULL) {
			$templateFile = t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/' . $default);
		}
		return parent::getTemplate($templateFile);
	}
	
}

?>