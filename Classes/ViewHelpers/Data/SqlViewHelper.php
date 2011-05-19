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
 * @subpackage ViewHelpers\Data
 */
class Tx_WildsideExtbase_ViewHelpers_Data_SqlViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {
	/**
	 * 
	 * @param string $name
	 * @param string $query
	 * @param string $table
	 * @param string $fields
	 * @param string $condition
	 * @param string $offset
	 * @param string $limit
	 * @param string $orderBy
	 * @param string $order
	 * @param boolean $silent
	 */
	public function render($name=NULL, $query=NULL, $table=NULL, $fields=NULL, $condition=NULL, $offset=NULL, $limit=NULL, $orderBy=NULL, $order=NULL, $silent=FALSE) {
		if (!$query && !$table) {
			$query = $this->renderChildren();
		} else if ($table && !$query) {
			$query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $table, $condition, $groupBy, $orderBy, $limit, $offset);
		}
		$result = $GLOBALS['TYPO3_DB']->sql($query);
		if (!$result) {
			if ($silent) {
				// important force-return here to avoid error messages caused by processing of $result
				return NULL;
			} else {
				return "<div>Invalid SQL query! Error was: " . mysql_error(). "</div>";
			}
		}
		$rows = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			array_push($rows, $row);
		}
		if (count($rows) == 0) {
			$value = "0";
		} else if (count($rows) == 1) {
			$value = array_pop($rows);
			if (count($value) == 1) {
				$value = array_pop($value);
			}
		} else {
			$value = $rows;
		}
		if ($name === NULL) {
			if (!$silent) {
				return $value;
			}
		} else {
			if ($this->templateVariableContainer->exists($name)) {
				$this->templateVariableContainer->remove($name);
			}
			$this->templateVariableContainer->add($name, $value);
		}
	}
	
}

?>