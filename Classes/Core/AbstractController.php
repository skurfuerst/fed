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
	protected $propertyMapper;

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
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	/**
	 * @var Tx_Fed_Utility_Debug
	 */
	protected $debugService;

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
	 * @param Tx_Fed_Utility_DocumentHead $documentHead
	 */
	public function injectDocumentHead(Tx_Fed_Utility_DocumentHead $documentHead) {
		$this->documentHead = $documentHead;
	}

	/**
	 * @param Tx_Fed_Utility_Debug $debugService
	 */
	public function injectDebugService(Tx_Fed_Utility_Debug $debugService) {
		$this->debugService = $debugService;
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
		$data = $this->fetchRestBodyData();
		$object = $this->fetchRestObject();
		$repository = $this->infoService->getRepositoryInstance($object);
		$extensionName = $this->infoService->getExtensionName($object);
		$storagePid = $this->getConfiguredStoragePid($extensionName);
		unset($data['uid']); // do NOT allow creation of UID=0
		$object = $this->extJSService->mapDataFromExtJS($object, $data);
		$object->setPid($storagePid);
		$repository->add($object);
		return $this->formatRestResponseData($object);
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return mixed
	 */
	private function performRestRead() {
		$object = $this->fetchRestObject();
		$repository = $this->infoService->getRepositoryInstance($object);
		$all = $repository->findAll()->toArray();
		$export = $this->extJSService->exportDataToExtJS($all);
		return $this->jsonService->encode($export);
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return stdClas
	 */
	private function performRestUpdate() {
		$data = $this->fetchRestBodyData();
		$object = $this->fetchRestObject();
		$repository = $this->infoService->getRepositoryInstance($object);
		$properties = $this->fetchRestBodyFields($body);
		$object = $this->extJSService->mapDataFromExtJS($object, $data);
		$repository->update($object);
		return $this->formatRestResponseData($object);
	}

	/**
	 * PURELY INTERNAL - CAN BE OVERRIDDEN
	 * @return void
	 */
	private function performRestDestroy() {
		$data = $this->fetchRestBodyData();
		$object = $this->fetchRestObject();
		$repository = $this->infoService->getRepositoryInstance($object);
		$target = $repository->findOneByUid($data['uid']);
		$repository->remove($target);
		#$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
		#$persistenceManager->persistAll();
		return $this->formatRestResponseData();
	}

	/**
	 * Fetch an instance of an aggregate root object as specified by the request parameters
	 * @return Tx_Extbase_DomainObject_AbstractEntity
	 * @api
	 */
	public function fetchRestObject() {
		$thisClass = get_class($this);
		$controllerName = $this->request->getArgument('controller');
		$objectClassname = str_replace("Controller_{$controllerName}Controller", 'Domain_Model_', $thisClass) . $controllerName;
		$object = $this->objectManager->get($objectClassname);
		return $object;
	}

	/**
	 * Returns associative array (with subarrays if necessary) of REST body
	 *
	 * @param string $body The request body to parse, empty for auto-fetch
	 * @return array
	 */
	public function fetchRestBodyData($body=NULL) {
		if ($body === NULL) {
			$body = file_get_contents("php://input");
		}
		$arr = array();
		$data = $this->jsonService->decode($body);
		foreach ($data as $k=>$v) {
			$arr[$k] = $v;
		}
		return $arr;
	}

	/**
	 * Fetch an associative array of fields posted as REST request body
	 *
	 * @param string $body The request body to parse, empty for auto-fetch
	 * @return array
	 * @api
	 */
	public function fetchRestBodyFields($body=NULL) {
		return array_keys($this->fetchRestBodyData($body));
	}

	/**
	 * Formats $data into a format agreable with ExtJS4 REST
	 *
	 * @param type $data Empty for NULL response
	 * @return mixed
	 */
	public function formatRestResponseData($data=NULL) {
		if ($data === NULL) {
			return "{}";
		}
		$responseData = $this->extJSService->exportDataToExtJS($data);
		$response = $this->jsonService->encode($responseData);
		return $response;
	}

	/**
	 * Get the current configured storage PID for $extensionName
	 * @param string $extensionName Optional extension name, empty for current extension name
	 * @return int
	 */
	public function getConfiguredStoragePid($extensionName=NULL) {
		$config = $this->getExtensionTyposcriptConfiguration($extensionName);
		if (is_array($config)) {
			return $config['persistence']['storagePid'];
		} else {
			return $GLOBALS['TSFE']->id;
		}
	}

	/**
	 * Fetches the TS config array from the current extension
	 * @param string $extensionName Optional extension name, empty for current extension name
	 * @return array
	 */
	public function getExtensionTyposcriptConfiguration($extensionName=NULL) {
		if ($extensionName === NULL) {
			$extensionName = $this->request->getExtensionName();
		}
		$extensionName = strtolower($extensionName);
		if (is_array($setup['plugin.']['tx_' . $pluginSignature . '.'])) {
			$extensionConfiguration = t3lib_div::array_merge_recursive_overrule($pluginConfiguration, Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_' . $pluginSignature . '.']));
		}
		return $extensionConfiguration;
	}

}

?>