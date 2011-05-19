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
 * Query for an Object - applies logic from data storage Query to any type of 
 * ObjectStorage, object or array. Filter a list of objects or a single object 
 * by creating a Query, setting original and applying constraints as you normally 
 * would to a Repository query. As a matter of fact, this class is to 
 * Tx_Extbase_Persistence_ObjectStorage, objects and arrays exactly what   
 * Tx_Extbase_Persistence_Query is to Tx_Extbase_Peristence_Repository. Enjoy.
 * 
 * This is particularly useful for a well-known, SQL-syntactical QueryObjectModel 
 * for data gotten NOT from SQL but for example posted via a plugin, read as JSON 
 * or simply gotten from a text file if you only need to perform VERY simple 
 * storage but still need to be able to filter it after reading it.
 * 
 * Note: QueryResult is also supported and returns a new QueryResult. However,
 * for QueryResult filtering you are more likely to get better performance out 
 * of performing this filtering in your Repository class (and if necessary for dynamic
 * filtering options, create your own findBy**** methods and allow additional
 * arguments defining the search).
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Object
 */
class Tx_Fed_Object_Query extends Tx_Extbase_Persistence_Query {
	
	/**
	 * @var mixed
	 */
	protected $original;
	
	/**
	 * Returns a filtered copy of the same type as $original.
	 * Subclasses under Object/Query all override this method with their own Query matching logic
	 * 
	 * @return mixed
	 * @api
	 */
	public function execute() {
		$type = $this->getOriginalType();
		$treatmentType = 'ObjectStorage'; // shifts to 'array', 'object' or 'QueryResult'. Default is 'ObjectStorage'
		if ($this->original instanceof ArrayAccess || is_array($this->original)) {
			$treatmentType = 'Array';
		} else if (is_object($this->instance) && $this->instance instanceof Tx_Extbase_Persistence_ObjectStorage === FALSE) {
			$treatmentType = 'Object';
		} else if ($this->original instanceof QueryResult) {
			$treatmentType = 'QueryResult';
		}
		$className = "Tx_Fed_Object_Query_{$treatmentType}";
		return $this->objectManager->get($className)->setOriginal($this->original)->execute();
	}
	
	/**
	 * Sets the originating object/array/ObjectStorage
	 * 
	 * @param mixed $original
	 * @api
	 */
	public function setOriginal($original) {
		$this->original = $original;
	}
	
	/**
	 * Gets the originating object/array/ObjectStorage
	 * 
	 * @return mixed
	 * @api
	 */
	public function getOriginal() {
		return $this->original;
	}
	
	/**
	 * Get the datatype of the originating object - determines how query should be processed
	 * 
	 * @return string
	 * @api
	 */
	public function getOriginalType() {
		return get_class($this->original);
	}
	
}

?>