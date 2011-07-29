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
 * Supports all usual access methods - which means you can iterate through this
 * in Fluid and access things like {fileFromStorage.filename} and metadata - see
 * Tx_Fed_Resource_File and others. Supports "serialization" to a TYPO3-compatible
 * CSV format based on $basePath for true BE support.
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Persistence
 */
class Tx_Fed_Persistence_FileObjectStorage extends SplObjectStorage {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * SITE-RELATIVE base path to prefix to all files' filenames in this collection
	 * @var string
	 */
	protected $basePath;

	/**
	 * Type of the object contained - such as Tx_Fed_Resource_File et al.
	 * Has a default value of the most basic File object type as fallback.
	 * @var string
	 */
	protected $objectType = 'Tx_Fed_Resource_File';

	/**
	 * The name of the property on a DomainObject to which this instance belongs.
	 * Necessary to resolve file uploads.
	 * @var string
	 */
	protected $associatedPropertyName;

	/**
	 * The associated DomainObject which has the property containing this object instance
	 * @var Tx_Extbase_DomainObject_AbstractDomainObject
	 */
	protected $associatedDomainObject;

	/**
	 * @param string $csv
	 */
	public function initializeFromCommaSeparatedValues($csv) {
		$files = explode(',', trim(',', $csv));
		foreach ($files as $file) {
			$fileObject = $this->objectManager->get($this->objectType, $this->basePath . $file);
			$this->attach($fileObject);
		}
	}

	/**
	 * @param string $basePath
	 */
	public function setBasePath($basePath) {
		if (substr($basePath, 0, 1) === DIRECTORY_SEPARATOR) {
			throw new Exception('FileObjectStorage does not support absolute paths!', 1311692821);
		}
		if (substr($basePath, -1) !== DIRECTORY_SEPARATOR) {
			$basePath .= DIRECTORY_SEPARATOR;
		}
		$this->basePath = $basePath;
	}

	/**
	 * @return string
	 */
	public function getBasePath() {
		return $this->basePath;
	}

	/**
	 * @param mixed $objectType
	 */
	public function setObjectType($objectType) {
		if (is_object($objectType)) {
			$this->objectType = get_class($objectType);
		} else {
			$this->objectType = $objectType;
		}
	}

	/**
	 * @return string
	 */
	public function getObjectType() {
		return $this->objectType;
	}

	/**
	 * Define the source parent of this FileObjectStorage - enables automatic
	 * detection of $objectType and $basePath based on $propertyName and TCA.
	 *
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $associatedDomainObject
	 * @param string $propertyName Name of the property in which this object is contained
	 */
	public function setAssociatedDomainObject(Tx_Extbase_DomainObject_AbstractDomainObject $associatedDomainObject, $propertyName) {
		$annotationValues = $this->infoService->getAnnotationValuesByProperty($associatedDomainObject, $propertyName, 'var');
		$complexType = array_pop($annotationValues);
		$table = $this->infoService->getDatabaseTable($associatedDomainObject);
		$underscoredPropertyName = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($propertyName);
		// use collected data to set necessary precursor variables
		$this->objectType = $this->infoService->parseObjectStorageAnnotation($complexType);
		$this->associatedDomainObject = $associatedDomainObject;
		$this->associatedPropertyName = $propertyName;
		$this->basePath = $this->infoService->getUploadFolder($associatedDomainObject, $propertyName);
	}

	/**
	 * @return Tx_Extbase_DomainObject_AbstractDomainObject
	 */
	public function getAssociatedDomainObject() {
		return $this->associatedDomainObject;
	}

	/**
	 * @param string $associatedPropertyName
	 */
	public function setAssociatedPropertyName($associatedPropertyName) {
		$this->associatedPropertyName = $associatedPropertyName;
	}

	/**
	 * CONSTRUCTOR. Allows setting a CSV (after setting ALL necessary precursors)
	 * to initialize a complete FileObjectStorage.
	 *
	 * Requires all values from one of these value sets to function properly:
	 *
	 * $basePath (optional, but must be set beforehand if necessary)
	 * $associatedPropertyName OR $objectType (to identify the object type)
	 * $objectType (if different from default Tx_Fed_Resource_File)
	 *
	 * -- OR --
	 *
	 * $associatedDomainObject, which fills $basePath with TCA uploadFolder
	 * $associatedPropertyName, which allows reflection to get exact $objectType
	 *
	 * @param string $possibleCsv
	 * @param string $possibleAssociatedPropertyName
	 */
	public function __construct($possibleCsv=NULL, $possibleAssociatedPropertyName=NULL) {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->infoService = $this->objectManager->get('Tx_Fed_Utility_DomainObjectInfo');
		$this->fileService = $this->objectManager->get('Tx_Fed_Service_File');
		if (is_string($possibleCsv)) {
			foreach (explode(',', $possibleCsv) as $filename) {
				$object = $this->objectManager->get($this->objectType, $this->basePath . $filename);
				$this->attach($object);
			}
		}
		if (is_string($possibleAssociatedPropertyName)) {
			$this->associatedPropertyName = $possibleAssociatedPropertyName;
		}
	}

	/**
	 * Converts the objectstorage to a TYPO3-compatible CSV value. Takes into
	 * account that $basePath may be set, in which case filenames without paths
	 * are concatenated to support TYPO3 DB "file" fields and upload folder.
	 * Files which are selected from within fileadmin for example will be
	 * concatenated with paths, making this compatible with both upload folder and
	 * direct file selection fields in the TYPO3 BE.
	 *
	 * Another note: if a serialization is performed a special check is made
	 * to see if there is an HTTP upload event taking place. If there is, and if
	 * this object has the $associatedPropertyName set, this looks for the file
	 * in the upload array, uses $basePath and TYPO3 unique-filename methods to
	 * simply upload the file and store the proper resulting filename instead.
	 * This "tricks" the persistence layer in Extbase into uploading the files
	 * for you exactly when it requests that the value of your DomainObject's
	 * property containing Tx_Fed_Persistence_FileObjectStorage<Tx_Fed_Resource_File>
	 * data for insertion into DB.
	 *
	 * This works as long as you have $associatedPropertyName; this is the hinge
	 * that lets Tx_Fed_Persistence_FileObjectStorage detect file uploads. So
	 * this should be set in either your DomainObject's constructor or the property's
	 * setter method.
	 * @return string
	 */
	public function __toString() {
		#var_dump($this->basePath);
		#exit();
		$filenames = array();
		foreach ($this as $fileObject) {
			if ($this->basePath) {
				$filename = $fileObject->getBasename();
				var_dump($filename);
			} else {
				$filename = $fileObject->getRelativePath();
			}
			#$filename = $fileObject->getRelativePath();
			array_push($filenames, $filename);
		}
		#exit();
		return implode(',', $filenames);
	}


}

?>