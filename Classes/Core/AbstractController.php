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
 * @subpackage Core
 */
abstract class Tx_Fed_Core_AbstractController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * @param Tx_Fed_Utility_JSON $jsonService
	 */
	public function injectJSONService(Tx_Fed_Utility_JSON $jsonService) {
		$this->jsonService = $jsonService;
	}

	public function getFlexForm() {
		$data = $this->request->getContentObjectData();
		$flexform = $data['pi_flexform'];
		$array = array();
		$dom = new DOMDocument();
		$dom->loadXML($flexform);
		foreach ($dom->getElementsByTagName('field') as $field) {
			$name = $field->getAttribute('index');
			$value = $field->getElementsByTagName('value')->item(0)->nodeValue;
			$value = trim($value);
			$array[$name] = $value;
		}
		return $array;
	}

	/**
	 * Handles special override requests for the "rest" action which is a reserved
	 * (now it is - sorry if that collides) action name to communicate with ExtJS
	 * REST services.
	 *
	 * @return string
	 */
	public function restAction() {
		$thisClass = get_class($this);
		$controllerName = $this->request->getArgument('controller');
		$repositoryClassname = str_replace("Controller_{$controllerName}Controller", 'Domain_Repository_', get_class($this)) . $controllerName . 'Repository';
		$repository = $this->objectManager->get($repositoryClassname);
		$all = $repository->findAll()->toArray();
		foreach ($all as $k=>$object) {
			$all[$k] = $this->infoService->getValuesByAnnotation($object, 'ExtJS');
		}

		$response = $this->jsonService->encode($all);
		echo $response;
		exit();
		return $response;
	}
}

?>