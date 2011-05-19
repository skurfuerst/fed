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
 * @subpackage Controller
 */
class Tx_Fed_Controller_RecordSelectorWidgetController extends Tx_Fed_Core_AbstractWidgetController {
	
	/**
	 * Default action
	 * 
	 * @return string
	 */
	public function indexAction() {
		
		
	}
	
	/**
	 * List all records based on simple conditions
	 * 
	 * @param int $storagePid UID of the sysfolder used to store records
	 * @param int $parent UID of the parent record
	 */
	public function listAction($storagePid=NULL, $parent=NULL) {
		
	}
	
	
	public function resolveAction() {
		
	}
	
	/**
	 * 
	 * @param string $q
	 * @param string $table
	 * @param string $titleField
	 * @param int $storagePid
	 * @param string $condition Additional SQL condition for query
	 */
	public function searchAction($q, $table, $titleField, $storagePid=0, $condition=NULL) {
		if ($condition === NULL) {
			$condition = "1 = 1";
		}
		if ($storagePid > 0) {
			$condition .= " AND pid = {$storagePid}";
		}
		$condition .= " AND deleted = 0";
		$condition .= " AND {$titleField} LIKE '%{$q}%'";
		$fields = "uid, {$titleField}";
		#$query = "SELECT {$fields} FROM {$table} WHERE {$condition} AND deleted = 0";
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $condition);
		$results = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$results[$row['uid']] = $row[$titleField];
		}
		return (string) json_encode($results);
	} 
	
	
}

?>