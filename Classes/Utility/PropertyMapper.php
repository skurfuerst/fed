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
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Utility
 * @deprecated Functionality replaced by Tx_Fed_Utility_DomainObjectReflection
 */
class Tx_Fed_Utility_PropertyMapper extends Tx_Fed_Utility_DomainObjectInfo implements t3lib_Singleton {

	/**
	 * Returns an array of property names and values by searching the $object
	 * for annotations based on $annotation and $value. If $annotation is provided
	 * but $value is not, All properties which simply have the annotation present.
	 * Relational values which have the annotation are parsed through the same
	 * function - sub-elements' properties are exported based on the same
	 * annotation and value
	 *
	 * @param mixed $object The object or classname to read
	 * @param string $annotation The annotation on which to base output
	 * @param string $value The value to search for; multiple values may be used in the annotation; $value must be present among them. If TRUE, all properties which have the annotation are returned
	 * @param boolean $addUid If TRUE, the UID of the DomainObject will be force-added to the output regardless of annotation
	 * @return array
	 * @deprecated Remains as legacy but related functionality moved to Tx_Fed_Utility_DomainObjectInfo
	 */
	public function getValuesByAnnotation($object, $annotation='json', $value=TRUE, $addUid=TRUE) {
		if (is_object($object)) {
			$className = get_class($object);
		} else {
			$className = $object;
			$object = $this->objectManger->get($className);
		}
		$this->recursionHandler->in();
		$this->recursionHandler->check($className);
		$properties = $this->reflectionService->getClassPropertyNames($className);
		$return = array();
		if ($addUid === TRUE) {
			$return['uid'] = $object->getUid();
		}
		foreach ($properties as $propertyName) {
			$tags = $this->reflectionService->getPropertyTagsValues($className, $propertyName);
			$getter = 'get' . ucfirst($propertyName);
			$annotationValues = $tags[$annotation];
			if (method_exists($object, $getter) === FALSE) {
				continue;
			}
			if ($annotationValues !== NULL && (in_array($value, $annotationValues) || $value === TRUE)) {
				$returnValue = $object->$getter();
				if ($returnValue instanceof Tx_Extbase_Persistence_ObjectStorage) {
					$array = $returnValue->toArray();
					foreach ($array as $k=>$v) {
						$array[$k] = $this->getValuesByAnnotation($v, $annotation, $value, $addUid);
					}
					$returnValue = $array;
				} else if ($returnValue instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
					$returnValue = $this->getValuesByAnnotation($returnValue, $annotation, $value, $addUid);
				}
				$return[$propertyName] = $returnValue;
			}
		}
		$this->recursionHandler->out();
		return $return;
	}


}




?>