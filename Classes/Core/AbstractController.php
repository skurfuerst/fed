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
	 * @var Tx_Extbase_Property_Mapper
	 */
	protected $proprtyMapper;

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 *
	 * @var Tx_Fed_Utility_ExtJS
	 */
	protected $extJSService;

	/**
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexform;

	/**
	 * @param Tx_Extbase_Property_Mapper $propertyMapper
	 */
	public function injectPropertyMapper(Tx_Extbase_Property_Mapper $propertyMapper) {
		$this->propertyMapper = $propertyMapper;
	}

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

	/**
	 * @param Tx_Fed_Utility_ExtJS $extJSService
	 */
	public function injectExtJSService(Tx_Fed_Utility_ExtJS $extJSService) {
		$this->extJSService = $extJSService;
	}

	/**
	 * @param Tx_Fed_Utility_FlexForm $flexform
	 */
	public function injectFlexFormService(Tx_Fed_Utility_FlexForm $flexform) {
		$this->flexform = $flexform;
	}

	/**
	 * Get the flexform definition from the current cObj instance
	 *
	 * @return array
	 * @api
	 */
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
	 * Handles special REST CRUD requests from ExtJS4 Model Proxies type "rest"
	 *
	 * @param string $crudAction String name of CRUD action (create, read, update or destroy)
	 * @return string
	 * @api
	 */
	public function restAction($crudAction='read') {
		switch ($crudAction) {
			case 'update': return $this->performRestUpdate();
			case 'destroy': return $this->performRestDestroy();
			case 'create': return $this->performRestCreate();
			case 'read':
			default: return $this->performRestRead();
		}
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return stdClas
	 */
	private function performRestCreate() {
		$repository = $this->fetchRestRepository();
		$body = file_get_contents('php://input');
		$data = $this->jsonService->decode($body);
		$object = $this->fetchRestObject();
		$properties = $this->fetchRestBodyFields($body);
		$object = $this->extJSService->mapDataFromExtJS($object, $data);
		return $this->extJSService->exportDataToExtJS($object);
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return mixed
	 */
	private function performRestRead() {
		$repository = $this->fetchRestRepository();
		$all = $repository->findAll()->toArray();
		$export = $this->extJSService->exportDataToExtJS($all);
		return $this->jsonService->encode($export);
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return stdClas
	 */
	private function performRestUpdate() {
		$repository = $this->fetchRestRepository();
		$body = file_get_contents("php://input");
		$data = $this->jsonService->decode($body);
		$object = $this->fetchRestObject($data->uid);
		$properties = $this->fetchRestBodyFields($body);
		if (in_array('uid', $properties)) {
			unset($properties[array_search('uid', $properties)]);
		}
		$mappingResult = $this->propertyMapper->map($properties, $data, $object);
		$responseData = $this->extJSService->exportDataToExtJS($object);
		$response = $this->jsonService->encode($responseData);
		return $response;
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return void
	 */
	private function performRestDestroy() {
		$repository = $this->fetchRestRepository();
	}

	/**
	 * Fetch an instance of an aggregate root object as specified by the request parameters
	 * @param int $uid
	 * @return stdClas
	 * @api
	 */
	public function fetchRestObject($uid=NULL) {
		if ($uid > 0) {
			$repository = $this->fetchRestRepository();
			return $repository->findOneByUid($uid);
		}
		$thisClass = get_class($this);
		$controllerName = $this->request->getArgument('controller');
		$objectClassname = str_replace("Controller_{$controllerName}Controller", 'Domain_Model_', $thisClass) . $controllerName;
		$object = $this->objectManager->get($objectClassname);
		return $object;
	}

	/**
	 * Fetch an instance of Repository which handles the aggregate root object this request is targetet towards
	 * @return Tx_Extbase_Persistence_Repository
	 * @api
	 */
	public function fetchRestRepository() {
		$thisClass = get_class($this);
		$controllerName = $this->request->getArgument('controller');
			$repositoryClassname = str_replace("Controller_{$controllerName}Controller", 'Domain_Repository_', $thisClass) . $controllerName . 'Repository';
		$repository = $this->objectManager->get($repositoryClassname);
		return $repository;
	}

	/**
	 * Fetch an associative array of fields posted as REST request body
	 * @return array
	 * @api
	 */
	public function fetchRestBodyFields($body) {
		if (!$body) {
			$body = file_get_contents("php://input");
		}
		$data = $this->jsonService->decode($body);
		$keys = array();
		foreach ($data as $k=>$v) {
			array_push($keys, $k);
		}
		return $keys;
	}

}

?>