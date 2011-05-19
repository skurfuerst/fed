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
 * ObjectStorage - adds a few smart features to the default ObjectStorage in 
 * Extbase, among other things pagination (with direct Fluid variable support), 
 * QueryObjectModel-type filtering (100% emulates the existing Repository-Query
 * way of filtering, run createQuery() on an ObjectStorage, apply constraints and 
 * $filteredObjectStorage = $queryFromObjectStorage->execute();
 * 
 * ALL of Extbase's QueryConstraint applications are supported and function just 
 * like they would on an SQL query.
 * 
 * Chained usage is possible, short example:
 * 
 * $filtered = $wsObjectStorage->createQuery()->limit(10)->offset(100)->execute();
 * 
 * Proxy methods have been added so you can do some things from Fluid if your 
 * DomainObjects defines that it wants Tx_Fed_Persistence_Objecstorage
 * instead of Tx_Extbase_Persistence_ObjectStorage. Indicate member types the same way
 * you would ObjectStorage: Tx_Fed_Persistence_Objecstorage<Tx_MyExt_Domain_Model_MyDomObj>
 * 
 * A note, though: anything but page and offset is a bit slow to use in Fluid templates
 * and is most definitely best suited for use in cachable plugins.
 * 
 * The short var {do.mn} stands for {domainObject.manyRelationProperty} which
 * is a property of a DomainObject which returns a Tx_Fed_Persistence_ObjectStorage.
 * 
 * {do.mn.page.5} // becomes a new, paginated ObjectStorage
 * {do.mn.offset.5} // use in loop argument to start from offset 5
 * {do.mn.limit.100} // use in loop argument to limit the number of loops
 * {do.mn.sort.name} // obvious, for loops (assumes ASC, "name" is property of members)
 * {do.mn.sort.name.DESC} // also obvious. ASC is default
 * 
 * Detailed example - wrapping output, for example for jQuery show/hide pages:
 * 
 * <f:for each="{do.mn.pages}" as="page" iteration="iteration">
 *     <div id="page{iteration.iteration}">
 *     <h4>Page {iteration.iteration}</h4>
 *     <f:for each="{page}" as="object">
 *         // rendering of each object
 *     </f:for>
 *     </div>
 * </f:for>
 * 
 * Note that this has a default itemsPerPage of 25. If you need a different value you
 * have three options: a) to always hard-limit, in which case use your DomainObject's 
 * setter method and run setItemsPerPage(50) before setting the internal property. Or 
 * b) getting the ObjectStorage, performing actions and the using the DomainObject's
 * setter method to refresh the value and finally c) $domainObject->getMultiRelation()->paginate($num);
 * to store the selected $num as itemsPerPage when this particular instance is referenced,
 * which it is every time the Domain Object property is accessed in Fluid.
 * 
 * Additional note on combining with Repository output:
 * 
 * If your Repository injects Tx_Fed_Object_QueryManager and 
 * simply does this in a find***** method:
 * 
 * $query = $this->createQuery();
 * $queryResult = $query->execute();
 * return $this->queryManager->promote($queryResult);
 * 
 * ... then instead of a QueryResult instance, an Tx_Fed_Persistence_ObjectStorage
 * full of objects is returned. Of course you can do this in your controller too but it makes 
 * a bit more sense in a Repository. Afterwards, you $this->view->assign('items', $outputItems);
 * and get support in your Fluid template for {items.page.5} for example.
 * 
 * The explanation is of course that Repositories' find*** usually return QueryResult 
 * which does not support the same features this class does. Running QueryManager's 
 * promote($queryResult) turns it into an objectstorage - and further allows you to detach,
 * attach etc. on multiresults from your repositories.
 * 
 * Finally, overloaded methods exists for "findBy" and "findOneBy", allowing you to treat 
 * your ObjectStorage as if it were a Repository. One result is returned as the proper type,
 * multiple results as new Tx_Fed_Persistence_ObjectStorage, You're welcome :)
 * 
 * As if that was not enough, direct returns are also supported, allowing this in Fluid
 * (assuming your DomainObject has boolean property "active" with getter and setter):
 * 
 * {do.mn.findByActive.1} // returns objects with boolean value TRUE in "active" property
 * 
 * Go nuts. 
 * 
 * {do.mn.findByActive.1.findByCategory.math.order.lastTeachingPeriod.DESC.limit.5} - to
 * get only DomainObjects with "active" = TRUE and "category" = 'math', order descending by last 
 * teaching period and finally limit to only five results... Phew. 
 * 
 * Only limitation is that only exact matching in the "findBy" and "findOneBy" methods is allowed and
 * naturally, only whole strings and numbers should be used as to not confuse Fluid's syntax 
 * detection. Zero and One works for boolean values and integers, all others considered string-match.
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Persistence
 */
class Tx_Fed_Persistence_ObjectStorage implements Countable, Iterator, ArrayAccess, Tx_Extbase_Persistence_ObjectMonitoringInterface  {
	
	/**
	 * This field is only needed to make debugging easier:
	 * If you call current() on a class that implements Iterator, PHP will return the first field of the object
	 * instead of calling the current() method of the interface.
	 * We use this unusual behavior of PHP to return the warning below in this case.
	 *
	 * @var string
	 */
	private $warning = 'You should never see this warning. If you do, you probably used PHP array functions like current() on the Tx_Fed_Persistence_ObjectStorage. To retrieve the first result, you can use the current() method.';

	/**
	 * An array holding the objects and the stored information. The key of the array items ist the 
	 * spl_object_hash of the given object.
	 *
	 * array(
	 * 	$this->hash($object) =>
	 * 		array(
	 *			'obj' => $object,
	 * 			'inf' => $information
	 *		)
	 * )
	 * 
	 * @var array
	 */
	protected $storage = array();

	/**
	 * Stores which field we are sorting members by
	 * 
	 * @var string
	 */
	protected $sortBy;
	
	/**
	 * Stores which property to sort by if the $sortBy value is itself a DomainObject
	 * 
	 * @var string $memberSortBy
	 */
	protected $memberSortBy;
	
	/**
	 * Stores if we are sorting ASC
	 * 
	 * @var boolean
	 */
	protected $sortAsc = FALSE;
	
	/**
	 * A flag indication if the object storage was modified after reconstitution (eg. by adding a new object)
	 * @var bool
	 */
	protected $isModified = FALSE;
		
	/**
	 * @var Tx_Fed_Object_QueryManager
	 */
	protected $queryManager;
	
	/**
	 * @var int
	 */
	protected $itemsPerPage = 25;
	
	/**
	 * @param Tx_Fed_Object_QueryManager $queryManager
	 */
	public function injectQueryManager(Tx_Fed_Object_QueryManager $queryManager) {
		$this->queryManager = $queryManager;
	}
	
	/**
	 * Quick search - tries to find matching objects through a very simple search.
	 * Only option possible is $exact=TRUE/FALSE - if you need other searches to be 
	 * performed you need to use $query = $objectStorage->createQuery(); and
	 * configure your query to your likings by QOM methods. You can chain this at 
	 * a fairly low execution time cost: 
	 * 
	 * $objectStorage->search('owner', $ownerUid)->search('name', $qStr);
	 * Which first filters to match only the user's items and then searches those 
	 * for matches of $qStr in property "name". Since $ownerUid is an integer, 
	 * the function assumes $exact=TRUE. Same goes for objects of any type. 
	 * DomainObjects are compared by classname and UID.
	 *  
	 * @param string $property Name of the property to search in 
	 * @param mixed $search
	 * @param boolean $exact
	 * @return Tx_Fed_Persistence_ObjectStorage
	 */
	public function search($property, $search, $exact=TRUE) {
		$query = $this->createQuery();
		if ($exact || is_integer($search) || is_object($search)) {
			$constraint = $query->equals($property, $search);
		} else {
			$constraint = $query->like($property, "%{$search}%");
		}
		$query->matching($constraint);
		return $query->execute();
	}
	
	/**
	 * Sets the number of items displayed per page
	 * 
	 * @param unknown_type $perPage
	 * @return void
	 * @api
	 */
	public function setItemsPerPage($perPage) {
		$this->itemsPerPage = $perPage;
	}
	
	/**
	 * Gets the number of items displayed per page
	 * 
	 * @return int
	 * @api 
	 */
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}
	
	/**
	 * Returns an array of "page" ObjectStorage instances, for chaining 
	 * in Fluid as {domainObject.manyRelation.page.5} for example. Depends on
	 * paginate() being run first in order to know how many items per page
	 * 
	 * @return array
	 * @api
	 */
	public function getPages() {
		$pages = array();
		$index = 0;
		$pageNumber = 0;
		foreach ($this as $item) {
			if (($index%$this->itemsPerPage === 0) || $index === 0) {
				$pageNumber++;
				$pages[$pageNumber] = $this->objectManager->get('Tx_Fed_Persistence_ObjectStorage');
			}
			$pages[$pageNumber]->attach($item);
			$index++;
		}
		return $pages;
	}
	
	/**
	 * Creates a query for searching through members of this ObjectStorage.
	 * Proxy for QueryManager::createQuery with $original = $this as parameter
	 * 
	 * @return Tx_Fed_Object_Query
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function createQuery() {
		return $this->queryManager->createQuery($this);
	}
	
	/**
	 * Paginage this ObjectStorage. Returns an array of O
	 * 
	 * @param int $itemsPerPage
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function paginate($itemsPerPage=-1) {
		if ($itemsPerPage > 0) {
			$this->setItemsPerPage($itemsPerPage);
		}
		return $this->getPages();
	}
	
	/**
	 * Limit this ObjectStorage (like SQL limit)
	 * 
	 * @param int $limit
	 * @return void
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function limit($limit) {
		$this->storage = array_slice($this->storage, 0, $limit, TRUE);
	}
	
	/**
	 * Offset this ObjectStorage (like SQL offset)
	 * 
	 * @param int $offset
	 * @return void
	 * @api
	 */
	public function offset($offset) {
		$this->storage = array_slice($this->storage, $offset, NULL, TRUE);
	}
	
	/**
	 * Slice this ObjectStorage - the internal storage gets updated
	 * 
	 * @param int $offset
	 * @param int $length
	 * @return void
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function slice($offset, $length=NULL) {
		$this->offset($offset);
		if ($length) {
			$this->limit($length);
		}  
	}
	
	/**
	 * Sort this ObjectStorage
	 * 
	 * @param string $sortBy Property to sort by, can be object but not array/ObjectStorage. DateTime supported (uses timestamp)
	 * @param string $order ASC or DESC
	 * @param string $memberSortBy If the value you sort on is itself a Domain Object, specify this as the property which contains the value to sort by
	 * @return Tx_Fed_Persistence_ObjectStorage
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function sort($sortBy, $order='ASC', $memberSortBy=NULL) {
		if ($this->count() == 0) {
			return $this;
		}
		$this->sortBy = $sortBy;
		$this->sortAsc = $order === 'ASC';
		$this->memberSortBy = $memberSortBy;
		$this->storage = uasort($this->storage, array($this, 'sortCompare'));
		return $this;
	}
	
	/**
	 * Comparison method for usort
	 * 
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 * @author Claus Due, Wildside A/S
	 */
	protected function sortCompare($a, $b) {
		if (is_string($a)) {
			$comp = strcmp($a, $b);
			if ($this->sortAsc === FALSE) {
				// reverse output
				if ($comp < 0) {
					return 1;
				} else if ($comp > 0) {
					return -1;
				} else {
					return $comp;
				}
			}
		} else if (is_numeric($a)) {
			if ($a === $b) {
				return 0;
			} else if ($a > $b) { 
				return $this->sortAsc ? 1 : -1;
			} else if ($a > $b) {
				return $this->sortAsc ? -1 : 1;
			}
		} else if (is_object($a)) {
			// throw if $a uses Iterator
			if ($a instanceof Iterator) {
				throw new Exception("Invalid sort property, cannot sort by Iterator", 1304870321);
			}
			if ($a instanceof DateTime) {
				$a = $a->getTimestamp();
			}
			if ($b instanceof DateTime) {
				$b = $b->getTimestamp();
			}
			if ($a instanceof Tx_Extbase_DomainObject_AbstractDomainObject) {
				if (!$this->memberSortBy) {
					throw new Exception("Asked to sort by DomainObject but memberSortBy parameter was not specified", 1304871195);
				}
				$getter = "get" . ucfirst($this->memberSortBy);
				$a = $a->$getter();
				$b = $b->$getter();
				return $this->sortCompare($a, $b);
			}
		} else if (is_array($a)) {
			throw new Exception("Invalid sort property, cannot sort by array", 1304870358);
		}
	}
	
	/**
	 * Adds a member before another identified member. Returns $this for chaining
	 * 
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $add
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $before
	 * @param mixed $information The data to associate with the object.
	 * @return Tx_Fed_Persistence_ObjectStorage
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function attachBefore(
			Tx_Extbase_DomainObject_AbstractDomainObject $add, 
			Tx_Extbase_DomainObject_AbstractDomainObject $before, 
			$information=NULL) {
		$this->rewind();
		if ($this->contains($add)) {
			return $this;
		} else if (isset($this->contains($before)) === FALSE) {
			$this->attach($add);
			return $this;
		} else {
			$this->insertAt($add, $before);
			return $this;
		}
	}
	
	/**
	 * Adds a member after another identified member. Returns $this for chaining
	 * 
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $add
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $after
	 * @param mixed $information The data to associate with the object.
	 * @return Tx_Fed_Persistence_ObjectStorage
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function attachAfter(
			Tx_Extbase_DomainObject_AbstractDomainObject $add, 
			Tx_Extbase_DomainObject_AbstractDomainObject $after,
			$information=NULL) {
		$this->rewind();
		if ($this->contains($add)) {
			return $this;
		} else if (isset($this->contains($after)) === FALSE) {
			$this->attach($add);
			return $this;
		} else {
			$this->insertAt($add, $after, FALSE, $information);
			return $this;
		}
	}
	
	
	/**
	 * Adds a member at a particular index, or end if index does not exist.
	 * Shifts other members one position down. Returns $this for chaining.
	 * 
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $add
	 * @param int $index
	 * @param mixed $information The data to associate with the object.
	 * @return Tx_Fed_Persistence_ObjectStorage
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function attachAt(Tx_Extbase_DomainObject_AbstractDomainObject $add, $index, $information) {
		$this->rewind();
		$storage = $this->objectManager->get(get_class(self));
		if ($index >= $this->count()) {
			$this->attach($add, $information);
		}
		foreach ($this as $key=>$item) {
			if ($currentIndex == $index) {
				$this->insertAt($add, $key, TRUE, $information);
				$this->detach($item);
				return $this;
			}
			$currentIndex++;
		}
		return $this;
	}
	
	/**
	 * Replace one member with another
	 * 
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $add
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $replace
	 * @param mixed $information
	 * @return Tx_Fed_Persistence_ObjectStorage
	 * @api
	 * @author Claus Due, Wildside A/S
	 */
	public function replace(
			Tx_Extbase_DomainObject_AbstractDomainObject $add,
			Tx_Extbase_DomainObject_AbstractDomainObject $replace,
			$information=NULL) {
		if ($this->contains($replace) === FALSE) {
			throw new Exception('Tried to replace a member which did not exist', 1304872573);
		}
		$hash = $this->hash($replace);
		foreach ($this as $key=>$item) {
			if ($key === $hash) {
				$this->insertAt($add, $index, TRUE, $information);
				$this->remove($item);
			}
		}
		return $this;
	}
	
	/**
	 * Adds an object to internal storage before/after a particular hash's index count
	 * PURELY INTERNAL
	 * 
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $object
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $offsetObject
	 * @param boolean $before If TRUE, inserts $object before position $hash, otherwise after
	 * @param mixed $information The data to associate with the object.
	 * @return void
	 * @author Claus Due, Wildside A/S
	 */
	protected function insertAt($object, $offsetObject, $before=TRUE, $information=NULL) {
		$this->rewind();
		$index = 0;
		$hash = $this->hash($offsetObject);
		foreach ($this as $key=>$item) {
			if ($key === $hash) {
				// insert here by splitting and splicing arrays
				if ($before === TRUE) {
					$index--;
				}
				if ($index < 0) {
					$index = 0;
				}
				$a1 = array_slice($this->storage, 0, $index, TRUE);
				$a2 = $index < count($this->storage) ? array_slice($this->storage, $index, NULL, TRUE) : array();
				$this->storage = $a1;
				$this->attach($object, $information);
				$this->storage = array_merge($this->storage, $a2);
				return;
			}
			$index++;
		}
	}
	
	/**
	 * Overloaded property getter. Makes a few predefined dynamic variables available (in Fluid, too),
	 * for example as {do.mn.limit.5}. See Tx_Fed_Object_AbstractOverloader.
	 * 
	 * @param string $name
	 * @return Tx_Fed_Object_AbstractOverloader Subclass of AbstractOverloader, implementing ArrayAccess
	 */
	public function __get($name) {
		switch ($name) {
			case 'limit': $overloader = 'Tx_Fed_Object_Limiter'; break;
			case 'offset': $overloader = 'Tx_Fed_Object_Offsetter'; break;
			case 'page': $overloader = 'Tx_Fed_Object_Paginater'; break;
			default: $overloader = NULL;
		}
		if ($overloader) {
			return $this->objectManager->get($overloader)->setOriginal($this);
		}
		return NULL;
	}
	
	/**
	 * Overloaded method caller. Handles findBy**** and others
	 * @param string $func
	 * @param array $arguments
	 */
	public function __call($func, $arguments) {
		
	}
	
	
	/**
	 * Rewind the iterator to the first storage element.
	 *
	 * @return void
	 */
	public function rewind() {
		reset($this->storage);
	}

	/**
	 * Checks if the array pointer of the storage points to a valid position
	 *
	 * @return void
	 */
	public function valid() {
		return current($this->storage);
	}

	/**
	 * Returns the index at which the iterator currently is. This is different from the SplObjectStorage 
	 * as the key in this implementation is the object hash.
	 *
	 * @return string The index corresponding to the position of the iterator.
	 */
	public function key() {
		return key($this->storage);
	}

	/**
	 * Returns the current storage entry.
	 *
	 * @return object The object at the current iterator position.
	 */
	public function current() {
		$item = current($this->storage);
		return $item['obj'];
	}

	/**
	 * Moves the iterator to the next object in the storage.
	 *
	 * @return void
	 */
	public function next() {
		next($this->storage);
	}

	/**
	 * Counts the number of objects in the storage.
	 *
	 * @return int The number of objects in the storage.
	 */
	public function count() {
		return count($this->storage);
	}

	/**
	 * Get SPL hash value for $object
	 * 
	 * @param mixed $object
	 */
	protected function hash($object) {
		return get_class($object) . ':' . $object->getUid();
	}
	
	/**
	 * Associate data to an object in the storage. offsetSet() is an alias of attach(). 
	 *
	 * @param object $object The object to add.
	 * @param mixed $information The data to associate with the object.
	 * @return void
	 */
	public function offsetSet($object, $information) {
		$this->isModified = TRUE;
		$this->storage[$this->hash($object)] = array('obj' => $object, 'inf' => $information);
	}

	/**
	 * Checks whether an object exists in the storage.
	 *
	 * @param string $object The object to look for.
	 * @return boolean Returns TRUE if the object exists in the storage, and FALSE otherwise.
	 */
	public function offsetExists($object) {
		return isset($this->storage[$this->hash($object)]);
	}

	/**
	 * Removes an object from the storage. offsetUnset() is an alias of detach().
	 *
	 * @param Object $object The object to remove.
	 * @return void
	 */
	public function offsetUnset($object) {
		$this->isModified = TRUE;
		unset($this->storage[$this->hash($object)]);
	}

	/**
	 * Returns the data associated with an object in the storage.
	 *
	 * @param string $object The object to look for.
	 * @return mixed The data previously associated with the object in the storage. 
	 */
	public function offsetGet($object) {
		return $this->storage[$this->hash($object)]['inf'];
	}

	/**
	 * Checks if the storage contains the object provided.
	 *
	 * @param Object $object The object to look for.
	 * @return boolean Returns TRUE if the object is in the storage, FALSE otherwise.
	 */
	public function contains($object) {
		return $this->offsetExists($object);
	}

	/**
	 * Adds an object inside the storage, and optionaly associate it to some data.
	 *
	 * @param object $object The object to add.
	 * @param mixed $information The data to associate with the object.
	 * @return void
	 */
	public function attach($object, $information = NULL) {
		$this->offsetSet($object, $information);
	}

	/**
	 * Removes the object from the storage.
	 *
	 * @param Object $object The object to remove.
	 * @return void
	 */
	public function detach($object) {
		$this->offsetUnset($object);
	}
	
	/**
	 * Returns the data, or info, associated with the object pointed by the current iterator position.
	 *
	 * @return mixed The data associated with the current iterator position.
	 */
	public function getInfo() {
		$item = current($this->storage);
		return $item['inf'];
	}
	
	/**
	 * @param array $data
	 */
	public function setInfo($data) {
		$this->isModified = TRUE;
		$key = key($this->storage);
		$this->storage[$key]['inf']  = $data;
	}

	/**
	 * Adds all objects-data pairs from a different storage in the current storage.
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage $storage The storage you want to import.
	 * @return void
	 */
	public function addAll(Tx_Extbase_Persistence_ObjectStorage $storage) {
		foreach ($storage as $object) {
			$this->attach($object, $storage->getInfo());
		}
	}

	/**
	 * Removes objects contained in another storage from the current storage.
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage $storage The storage containing the elements to remove.
	 * @return void
	 */
	public function removeAll(Tx_Extbase_Persistence_ObjectStorage $storage) {
		foreach ($storage as $object) {
			$this->detach($object);
		}
	}
	
	/**
	 * Returns this object storage as an array
	 *
	 * @return array The object storage
	 */
	public function toArray() {
		$array = array();
		foreach ($this->storage as $item) {
			$array[] = $item['obj'];
		}
		return $array;
	}

	public function serialize() {
		throw new RuntimeException('An ObjectStorage instance cannot be serialized.', 1267700868);
	}

	public function unserialize($serialized) {
		throw new RuntimeException('A ObjectStorage instance cannot be unserialized.', 1267700870);
	}
	
	/**
	 * Register an object's clean state, e.g. after it has been reconstituted
	 * from the database
	 *
	 * @return void
	 */
	public function _memorizeCleanState() {
		$this->isModified = FALSE;
	}

	/**
	 * Returns TRUE if the properties were modified after reconstitution
	 *
	 * @return boolean
	 */
	public function _isDirty() {
		return $this->isModified;
	}
	
	
}

?>