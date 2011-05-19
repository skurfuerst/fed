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
class Tx_Fed_ViewHelpers_Extbase_Widget_TimelineViewHelper extends Tx_Fed_ViewHelpers_Extbase_WidgetViewHelper {
	
	
	/**
	 * 
	 * @param array $objects
	 */
	public function render($objects) {
		
		$json = array();
		$config = new stdClass();
		$config->id = 'jshist';
		$config->title = 'Timeline';
		$config->focus_date = date('Y-m-d H:i:s');
		$config->initial_zoom = 20;
		$config->events = array();
		
		foreach ($objects as $object) {
			$time = $object->getTstamp();
			$date = date('Y-m-d H:i:s', $time);
			$item = new stdClass();
			$item->id = 'jshist-'.$object->getUid();
			$item->title = $object->getTitle();
			$item->description = $object->getDescription();
			$item->startdate = $date;
			$item->enddate = $date;
			$item->link = '';
			$item->importance = 40;
			$item->icon = '';
			array_push($config->events, $item);
		}
		
		array_push($json, $config);
		
		$temp = json_encode($json);
		$md5 = md5($temp);
		
		$tempJSON = PATH_site . '/typo3temp/'. $md5 . '.json';
		file_put_contents($tempJSON, $temp);
		$children = $this->renderChildren();
		$html = <<< HTML
<div id='timeline' style="width: 500px; height: 300px;"></div>

<div class='controls'>
<table>
<tr><td id='zoomlevel' width='33%'>...</td><td id='tickwidth' width='33%'>tw</td><td id='focusdate'>...</td></tr>
<tr><td id='tickpos'></td><td colspan='2' id='note'>...</td></tr>
</table>
</div>

{$children}
HTML;
		
		$script = <<< SCRIPT
jQuery(document).ready(function () {
	var glider = jQuery("#timeline").timeline({
	"min_zoom":20,
	"max_zoom":70,
	"initial_timeline_id": "jshist",
	"data_source":"/typo3temp/{$md5}.json"
	});
});

SCRIPT;
		
		$relPath = t3lib_extMgm::siteRelPath('fed');
		$this->includeHeader($script, 'js');
		$this->includeFile($relPath . 'Resources/Public/Javascript/com/timeglider/jquery-ui-1.8.5.custom.css');
		$this->includeFile($relPath . 'Resources/Public/Stylesheet/Timeglider.css');
		$this->includeFile($relPath . 'Resources/Public/Javascript/com/timeglider/jquery-ui-1.8.9.custom.min.js');
		$this->includeFile($relPath . 'Resources/Public/Javascript/com/timeglider/timeglider-0.0.7.min.js');
		return $html;
		
	}
	
}


?>