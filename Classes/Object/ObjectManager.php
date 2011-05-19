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
 * Mini shortcut for loading DomainObjects based on their string representations; otherwise
 * just loads whatever Extbase's ObjectManager would load. 
 * 
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Object
 */
class Tx_Fed_Object_ObjectManager extends Tx_Extbase_Object_ObjectManager implements t3lib_Singleton {
	
	
	/**
	 * Returns a fresh or existing instance of the object specified by $objectName.
	 *
	 * Important:
	 *
	 * If possible, instances of Prototype objects should always be created with the
	 * Object Manager's create() method and Singleton objects should rather be
	 * injected by some type of Dependency Injection.
	 *
	 * @param string $objectName The name of the object to return an instance of
	 * @return object The object instance
	 * @api
	 */
	public function get($name) {
		$arguments = func_get_args();
		$raw = array_shift($arguments);
		$exploded = explode(':', $name);
		if (count($exploded) == 2 && intval($exploded[1]) > 0) {
			// likelihood: DomainObject with UID as string representation - detect further
			$uid = intval(array_pop($exploded));
			$name = array_pop($exploded);
			$argument = $this->get('Tx_Extbase_MVC_Controller_Argument', 'content', $raw);
			$instance = $argument->setValue($uid)->getValue(); // hopefully a transformation to an object
			if ($object instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
				// certainty; is DomainObject - return it
				return $instance;
			}
		} else if (count($arguments) == 1 && strpos($name, '_Domain_Model_')) {
			$arg = array_shift($arguments);
			if (is_array($arg) && count($arg) > 0) {
				// likelihood: $arg is an array of UIDs - detect further. Instanciate 
				// an ObjectStorage in case we need to attach loaded candidates.
				$objectStorage = $this->get('Tx_Fed_Persistence_ObjectStorage');
				$isUidArray = TRUE;
				foreach ($arg as $possibleUid) {
					// absolutely only positive integers are accepted - a single value which is not 
					// a positive integer means this is NOT an array of UIDs
					if (intval($possibleUid) < 1) {
						$isUidArray = FALSE;
					}
				}
				if ($isUidArray) {
					// certainty: UID array specified. Load the instances we can. Respect Repository
					// for loading - storagePid etc also. Repository with wrong/missing storagePid
					// returns empty QueryResult; this method with wrong/missing storagePid returns
					// empty ObjectStorage
					foreach ($arg as $uid) {
						$instance = $this->get($name, $uid);
						if ($instance) {
							$objectStorage->attach($instance);
						}
					}
					return $objectStorage;
				} else {
					return $this->get($name, $arg);
				}
			}
			$uid = intval($arg);
			if ($uid > 0) {
				// is DomainObject and possible UID was only argument; retry as string representation
				return $this->get("{$name}:{$uid}");
			}
		}
		return $this->objectContainer->getInstance($name, $arguments);
	}
	
}

?>