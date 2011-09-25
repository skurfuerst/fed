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
 * Bootstrap wrapper for special JSON-only communication between AJAX Widgets and Extbase Controllers
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Core
 */

class Tx_Fed_Core_Bootstrap extends Tx_Extbase_Core_Bootstrap {

	private $mapper;
	private $jsonService;

	/**
	 * @param Tx_Fed_Object_ObjectManager $objectManager
	 */
	public function injectCustomObjectManager(Tx_Fed_Object_ObjectManager $objectManager) {
		$this->injectObjectManager($objectManager);
	}

	/**
	 * Runs the request
	 *
	 * @param string $content
	 * @param array $configuration
	 */
	public function run($content, $configuration) {
		$this->initialize($configuration);
		$this->mapper = $this->objectManager->get('Tx_Fed_Utility_PropertyMapper');
		$this->jsonService = $this->objectManager->get('Tx_Fed_Utility_JSON');
		$messager = $this->objectManager->get('Tx_Extbase_MVC_Controller_FlashMessages');
		$requestHandlerResolver = $this->objectManager->get('Tx_Extbase_MVC_RequestHandlerResolver');
		$requestHandler = $requestHandlerResolver->resolveRequestHandler();
		$response = $requestHandler->handleRequest();
		if ($response === NULL) {
			return;
		}
		$this->resetSingletons();

		$content = $response->getContent();
		$testJSON = $this->jsonService->decode($content);
		$object = $this->detectModelObject($content);
		if (is_array($object) && !$testJSON) {
			$data = $object;
		} else if (is_array($testJSON)) {
			foreach ($testJSON as $k=>$v) {
				$testJSON[$k] = $this->detectModelObject($v);
			}
			$data = $testJSON;
		} else if (is_object($testJSON)) {
			foreach ($testJSON as $k=>$v) {
				$testJSON->$k = $this->detectModelObject($v);
			}
			$data = $testJSON;
		} else {
			$data = $content;
		}
		$messager->getAllMessagesAndFlush();

		if (is_array($data) || is_object($data)) {
			$output = $this->jsonService->encode($data);
		} else {
			$output = $data;
		}
		return $output;
	}

	private function detectModelObject($content) {
		if (is_string($content) === FALSE) {
			return $content;
		}
		list ($dataType, $uid) = explode(':', $content);
		if (class_exists($dataType) && intval($uid) > 0) {;
			$repositoryClass = str_replace('_Model_', '_Repository_', $dataType) . 'Repository';
			$repository = $this->objectManager->get($repositoryClass);
			$object = $repository->findOneByUid($uid);
			if ($object) {
				$data = $this->mapper->getValuesByAnnotation($object, 'json', TRUE);
			} else {
				$data = NULL;
			}
		} else {
			$data = $content;
		}
		return $data;
	}

}

?>