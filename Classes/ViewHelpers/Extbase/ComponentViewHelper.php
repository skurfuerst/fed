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
 * Creates a Component container
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\Extbase
 */
class Tx_Fed_ViewHelpers_Extbase_ComponentViewHelper extends Tx_Fed_Core_ViewHelper_AbstractExtbaseViewHelper {
	
	/**
	 * Register arguments for this ViewHelper
	 */
	public function initializeArguments() {
		$this->registerArgument('objects', 'array', 'Objects to render', FALSE, NULL);
		$this->registerArgument('strategy', 'string', '"lazy" or "eager" - lazy queues requests, eager does not', FALSE, 'eager');
	}
	
	/**
	 * Render an entry for a Listener compatible with JS
	 * @return string
	 */
	public function render() {
		if ($this->hasArgument('objects')) {
			$this->setObject($this->arguments['objects']->current());
		}
		return $this->renderChildren();
	}
	
	public function renderChildren() {
		$json = array(
			'displayType' => $this->arguments['displayType'],
			'controller' => $this->arguments['controllerName'],
			'action' => $this->arguments['action'],
			'pageUid' => $this->arguments['pageUid'],
			'extensionName' => $this->arguments['extensionName'],
			'title' => $this->arguments['name'],
			'strategy' => $this->arguments['strategy'],
			'bulk' => 0
		);
		if ($objects && $strategy == 'lazy') {
			// analyze for Controller name and type:
			$probe = $objects->current();
			$probeClass = get_class($probe);
			$upperCamelAction = ucfirst($action);
			$controllerClass = str_replace("_Domain_Model", "_Controller_", $probeClass) . "Controller";
			$bulkAction = "bulk{$upperCamelAction}Action";
			if (method_exists($controllerClass, $bulkAction)) {
				$obj['bulk'] = 1;
				$obj['action'] = $bulkAction;
			}
		}
		$json = array_merge($this->arguments['config'], $json);
		$json = $this->jsonService->encode($obj);
		
		$html = "<div class='fed-component {$class}'>" . chr(10);
		$html .= "<div class='fed-json'>{$json}</div>" . chr(10);
		$html .= $this->renderChildren() . chr(10);
		$html .= "</div>" . chr(10);
		return $html;
	}
	
}

?>