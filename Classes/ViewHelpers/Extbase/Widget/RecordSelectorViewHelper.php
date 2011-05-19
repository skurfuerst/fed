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
 * @subpackage ViewHelpers\Extbase\Widget
 */
class Tx_Fed_ViewHelpers_Extbase_Widget_RecordSelectorViewHelper extends Tx_Fed_ViewHelpers_Extbase_WidgetViewHelper {
	
	const NS = 'dk.wildside.display.widget.RecordSelectorWidget';
	
	private $name;
	private $table;
	private $query;
	private $titleField;
	private $storagePid;
	
	/**
	 * Render an entry for a Listener compatible with JS LocusController
	 * @param string $widget JS namespace of widget to use - override this if you subclassed dk.wildside.display.widget.RecordSelectorWidget in JS
	 * @param string $name Name of the (emulated) property
	 * @param Tx_Extbase_Persistence_ObjectStorage $data Prefilled records
	 * @param string $class Extra CSS-classes to use
	 * @param string $title Title of the widget
	 * @param int $page If specified, calls controller actions on this page uid
	 * @param string $templateFile siteroot-relative path of template file to use
	 * @param string $table The name of the table containing records 
	 * @param string $titleField Name of the field or dot-concatenated field names
	 * @param int $storagePid PID of the sysfolder or page where records are stored   
	 * @param int $type TypeNum, if any, for building request URI
	 * @param string $relationType Either '1:1', '1:n' or 'm:n' - affects how the field's values are returned. A single value is returned for 1:1, array of values for the others.
	 * @param boolean $preload If TRUE, all possible search results are preloaded
	 * @param string $condition If set, adds $condition as SQL condition for search query and list-all
	 * @return string
	 */
	public function render(
			$widget=self::NS,
			$name='records', 
			$data=NULL, 
			$class=NULL, 
			$title=NULL,
			$page=NULL,
			$templateFile=NULL,
			$table='pages',
			$titleField='title',
			$storagePid=0,
			$type=4815162342,
			$relationType='1:n',
			$preload=FALSE,
			$condition=NULL) {
		$this->name = $name;
		$this->table = $table;
		$this->query = $query;
		$this->titleField = $titleField;
		$this->storagePid = $storagePid;
		
		$config = new stdClass();
		$config->table = $table;
		$config->titleField = $titleField;
		$config->relationType = $relationType;
		$config->storagePid = $storagePid;
		$config->condition = $condition;
		if ($preload) {
			$config->preload = $preload;
		};
		
		$controller = 'RecordSelectorWidget';
		$action = 'search';
		$plugin = 'tx_fed_api';
		$html = $this->renderChildren();
		if ($data instanceof Tx_Extbase_Persistence_ObjectStorage == FALSE && is_array($data) == FALSE) {
			$data = array($data);
		}
		if (strlen(trim($html)) == 0) {
			$defaultTemplateFile = 'Widget/RecordSelectorWidget.html';
			$template = $this->getTemplate($templateFile, $defaultTemplateFile);
			if ($preload) {
				$template->assign('available', $this->getPossibles());
			}
			$template->assign('selected', $data);
			$html = $template->render();
		}
		return parent::render($widget, $name, $controller, $action, $page, $plugin, $data, $class, $title, $type, $html, $config);
	}
	
	private function getPossibles() {
		return $this->getRecords();
	}
	
	private function getSelected($values) {
		if (count($values) >= 1) {
			$condition = "uid IN (" . implode(',', $values) . ")";
		} else {
			$condition = "1=0";
		}
		return $this->getRecords($condition);
	}
	
	private function getRecords($condition='1=1') {
		$condition .= " AND deleted = 0";
		if ($this->table == 'fe_users') {
			$condition .= " AND disable = 0";
		} else {
			$condition .= " AND hidden = 0";
		}
		if ($this->storagePid) {
			$condition .= " AND pid = '{$this->storagePid}'";
		}
		$array = array();
		$fields = implode(', ', array_merge(explode('.', $this->titleField), array('uid')));
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $this->table, $condition);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$title = $row[$this->titleField];
			$array[$row['uid']] = $title;
		}
		return $array;
	}
	
	
	
	
	
}

?>