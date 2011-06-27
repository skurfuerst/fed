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
 * FlexForm integration Service
 *
 * Capable of returning instances of DomainObjects or ObjectStorage from
 * FlexForm field values if the type of field is a database relation and the
 * table it uses is one associated with Extbase.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Fed_Utility_FlexForm implements t3lib_Singleton {

	/**
	 * @var string
	 */
	protected $raw;

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 *
	 * @var Tx_Extbase_Configuration_FrontendConfigurationManager
	 */
	protected $configuration;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * @param Tx_Extbase_Configuration_FrontendConfigurationManager $configurationManager
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_FrontendConfigurationManager $configurationManager) {
		$this->configuration = $configurationManager;
	}

	/**
	 * @param Tx_Extbase_Service_FlexFormService $flexFormService
	 */
	public function injectFlexFormService(Tx_Extbase_Service_FlexFormService $flexFormService) {
		$this->flexFormService = $flexFormService;
	}

	/**
	 * Initialization
	 */
	public function initializeObject() {
		$cObj = $this->configuration->getContentObject();
		$this->raw = $cObj->data['pi_flexform'];
		if ($this->raw) {
			$languagePointer = 'lDEF';
			$valuePointer = 'vDEF';
			$this->storage = $this->convertFlexFormContentToArray($this->raw, $languagePointer, $valuePointer);
		} else {
			return array();
		}
	}

	/**
	 * Gets the value of the FlexForm field. As string. You must parse it
	 * manually - for now. UNLESS your value is a DomainObject record reference or
	 * a list of references to DomainObject records. In which case there's no
	 * reason why not to turn the value into ObjectStorage<ModelObject> or
	 * ModelObject:UID instances.
	 *
	 * @param boolean $applyTransformations If TRUE, transforms according to datatype - Extbase objects supported
	 * @return string
	 * @api
	 */
	public function getAll($applyTransformations=TRUE) {
		return $this->get(NULL, $applyTransformations);
	}

	/**
	 * Get a single field's value (or all values if no $key given;
	 * getAll() is an alias of get() with no argument)
	 *
	 * @param string $key
	 * @param boolean $applyTransformations If TRUE, transforms according to datatype - Extbase objects supported
	 * @return mixed
	 * @api
	 */
	public function get($key=NULL, $applyTransformations=TRUE) {
		if ($key === NULL) {
			$arr = $this->storage;
			foreach ($arr as $k=>$v) {
				$arr[$k] = $this->get($k);
			}
			return $arr;
		}
		if ($applyTransformations) {
			return $this->transform($key);
		} else {
			return $this->storage[$key];
		}
	}

	/**
	 * Sets a value back in the flexform. For relational fields supporting
	 * Extbase DomainObjects, the $value may be an ObjectStorage or ModelObject
	 * instance - or the regular, oldschool CSV/UID string value
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value) {
		$this->storage[$key] = $value;
	}

	/**
	 * Write the FlexForm back from whence it came. Returns TRUE/FALSE
	 * on success/failure.
	 *
	 * @return boolean
	 */
	public function save() {
		return FALSE;
	}

	/**
	 * Returns the type of $key in the FlexForm. Returns Classname if relational
	 * value and Tx_Extbase_Perstence_ObjectStorage<ClassName> for multirelations.
	 *
	 * @param string $key
	 * @return string
	 */
	public function getFieldType($key) {

	}

	/**
	 * Determines wether or not $key is a relation to a table known to Extbase
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function isExtbaseRelation($key) {
		$type = $this->getFieldType($key);
		if (class_exists($type)) {
			// 1:1 relation
			return (bool) TRUE;
		}
		if (class_exists($this->infoService->parseObjectStorageAnnotation($type))) {
			return (bool) TRUE;
		}
		return (bool) FALSE;
	}

	public function transform($name) {
		return $this->storage[$name];
	}

		/**
	 * Parses the flexForm content and converts it to an array
	 * The resulting array will be multi-dimensional, as a value "bla.blubb"
	 * results in two levels, and a value "bla.blubb.bla" results in three levels.
	 *
	 * Note: multi-language flexForms are not supported yet
	 *
	 * @param string $flexFormContent flexForm xml string
	 * @param string $languagePointer language pointer used in the flexForm
	 * @param string $valuePointer value pointer used in the flexForm
	 * @return array the processed array
	 */
	public function convertFlexFormContentToArray($flexFormContent, $languagePointer = 'lDEF', $valuePointer = 'vDEF') {
		$settings = array();

		$flexFormArray = t3lib_div::xml2array($flexFormContent);
		$flexFormArray = (isset($flexFormArray['data']) ? $flexFormArray['data'] : array());
		foreach(array_values($flexFormArray) as $languages) {
			if (!is_array($languages[$languagePointer])) {
				continue;
			}

			foreach($languages[$languagePointer] as $valueKey => $valueDefinition) {
				if (strpos($valueKey, '.') === false) {
					$settings[$valueKey] = $this->walkFlexFormNode($valueDefinition, $valuePointer);
				} else {
					$valueKeyParts = explode('.', $valueKey);
					$currentNode =& $settings;

					foreach ($valueKeyParts as $valueKeyPart) {
						$currentNode =& $currentNode[$valueKeyPart];
					}

					if (is_array($valueDefinition)) {
						if (array_key_exists($valuePointer, $valueDefinition)) {
							$currentNode = $valueDefinition[$valuePointer];
						} else {
							$currentNode = $this->walkFlexFormNode($valueDefinition, $valuePointer);
						}
					} else {
						$currentNode = $valueDefinition;
					}
				}
			}
		}
		return $settings;
	}

	/**
	 * Parses a flexForm node recursively and takes care of sections etc
	 *
	 * @param array $nodeArray The flexForm node to parse
	 * @param string $valuePointer The valuePointer to use for value retrieval
	 * @return array
	 */
	public function walkFlexFormNode($nodeArray, $valuePointer = 'vDEF') {
		if (is_array($nodeArray)) {
			$return = array();

			foreach ($nodeArray as $nodeKey => $nodeValue) {
				if ($nodeKey === $valuePointer) {
					return $nodeValue;
				}

				if (in_array($nodeKey, array('el', '_arrayContainer'))) {
					return $this->walkFlexFormNode($nodeValue, $valuePointer);
				}

				if (substr($nodeKey, 0, 1) === '_') {
					continue;
				}

				if (strpos($nodeKey, '.')) {
					$nodeKeyParts = explode('.', $nodeKey);
					$currentNode =& $return;

					for ($i = 0; $i < (count($nodeKeyParts) - 1); $i++) {
						$currentNode =& $currentNode[$nodeKeyParts[$i]];
					}

					$newNode = array(next($nodeKeyParts) => $nodeValue);
					$currentNode = $this->walkFlexFormNode($newNode, $valuePointer);
				} else if (is_array($nodeValue)) {
					if (array_key_exists($valuePointer, $nodeValue)) {
						$return[$nodeKey] = $nodeValue[$valuePointer];
					} else {
						$return[$nodeKey] = $this->walkFlexFormNode($nodeValue, $valuePointer);
					}
				} else {
					$return[$nodeKey] = $nodeValue;
				}
			}
			return $return;
		}

		return $nodeArray;
	}


}
?>
