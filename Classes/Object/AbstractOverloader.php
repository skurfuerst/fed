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
 * Abstract Overloader
 * 
 * This base class can be used to create special objects which mask
 * array/object/QueryResult/ObjectStorage instances in a special accessor.
 * 
 * It works this way:
 * 
 * You subclass this class and defined your offsetGet($offset) method.
 * You get the original through $this->getOriginal(); and perform whatever
 * filtering/conditioning/manipulation of $original you need to, before you 
 * finally return that object.
 * 
 * Additionally you can override the offsetSet($offset, $value); method 
 * if you need to be able to also set your values.
 * 
 * The result:
 * 
 * $mask = $objectManager->get('Tx_MyExt_Object_MyOverloadSubclass')->setOriginal($objectStorage);
 * $this->view->assign('maskVar', $mask);
 * 
 * In PHP:
 * $filtered = $mask->someMagicOffset; // OR $mask['someMagicOffset'];
 * 
 * And in Fluid:
 * {maskVar.someMagicOffset}
 * 
 * This passes "someMagicOffset" to your offsetGet($offset) method as the $offset parameter.
 * Now do your worst. And by that I mean your best. :)
 * 
 * For an ObjectStorage value this means you can iterate it or call whichever methods
 * you like - take a look at Tx_Fed_Persistence_ObjectStorage to see 
 * if you should replace your base classes for ObjectStorage instances.
 * 
 * In PHP:
 * $numItems = $mask->someMagicOffset->count();
 * 
 * In Fluid:
 * <f:for each="{maskVar.someMagicOffset}" as="filteredItem">
 * ...
 * </f:for>
 * 
 * If you choose to use Tx_Fed_Persistence_ObjectManager as your base class
 * you get a lot of extra features in Fluid. See the extensive comment in the class file.
 * 
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Object
 */
abstract class Tx_Fed_Object_AbstractOverloader implements ArrayAccess {
	
	/**
	 * @var mixed
	 */
	protected $original;
	
	/**
	 * Sets the originating object/array/ObjectStorage
	 * @param mixed $original
	 * @return Tx_Fed_Object_AbstractOverloader
	 * @api
	 */
	public function setOriginal($original) {
		$this->original = $original;
		return $this;
	}
	
	/**
	 * Gets the originating object/array/ObjectStorage
	 * @return mixed
	 * @api
	 */
	public function getOriginal() {
		return $this->original;
	}
	
	/**
	 * @param offset
	 */
	public function offsetExists ($offset) {
		return FALSE;
	}

	/**
	 * MUST BE OVERRIDDEN BY SUBCLASSER!
	 * @param offset
	 */
	public function offsetGet ($offset) {
		return NULL;
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet ($offset, $value) {
		return NULL;
	}

	/**
	 * @param offset
	 */
	public function offsetUnset ($offset) {
		return NULL;
	}
	
	/**
	 * Object-access alias for ArrayAccess $offset
	 * @param mixed $offset
	 */
	public function __get($offset) {
		return $this->offsetGet($offset);
	}
	
}