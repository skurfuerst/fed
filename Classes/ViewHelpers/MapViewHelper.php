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
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Map
 */
class Tx_Fed_ViewHelpers_MapViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	protected $tagName = 'div';

	public function initializeArguments() {
		$markerIcon = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Icons/MapMarker.png';
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('lat', 'float', 'Lattitude');
		$this->registerTagAttribute('lng', 'float', 'Longitude');
		$this->registerTagAttribute('icon', 'string', 'Icon filename', FALSE, $markerIcon);
		$this->registerTagAttribute('iconCenterX', 'int', 'Icon pivot coordinate X');
		$this->registerTagAttribute('iconCenterY', 'int', 'Icon pivot coordinate Y');
		$this->registerArgument('mapTypeId', 'string', 'Type of map to display, default google.maps.MapTypeId.ROADMAP', FALSE, 'google.maps.MapTypeId.ROADMAP');
	}

	/**
	 * @param string $api
	 * @param string $width
	 * @param string $height
	 * @param string $backgroundColor Color used for the background of the Map div. This color will be visible when tiles have not yet loaded as the user pans. This option can only be set when the map is initialized.
	 * @param boolean $disableDefaultUI Enables/disables all default UI. May be overridden individually.
	 * @param boolean $disableDoubleClickZoom Enables/disables zoom and center on double click. Enabled by default.
	 * @param boolean $draggable If false, prevents the map from being dragged. Dragging is enabled by default.
	 * @param string $draggableCursor The name or url of the cursor to display on a draggable object.
	 * @param string $draggingCursor The name or url of the cursor to display when an object is dragging.
	 * @param string $keyboardShortcuts If false, prevents the map from being controlled by the keyboard. Keyboard shortcuts are enabled by default.
	 * @param boolean $mapTypeControl The initial enabled/disabled state of the Map type control.
	 * @param float $maxZoom The maximum zoom level which will be displayed on the map. If omitted, or set to null, the maximum zoom from the current map type is used instead.
	 * @param float $minZoom The minimum zoom level which will be displayed on the map. If omitted, or set to null, the minimum zoom from the current map type is used instead.
	 * @param boolean $noClear If true, do not clear the contents of the Map div.
	 * @param boolean $panControl The enabled/disabled state of the pan control.
	 * @param boolean $scaleControl The initial enabled/disabled state of the scale control.
	 * @param boolean $scrollwheel If false, disables scrollwheel zooming on the map. The scrollwheel is enabled by default.
	 * @param boolean $streetViewControl The initial enabled/disabled state of the Street View pegman control.
	 * @param float $zoom The initial Map zoom level. Required.
	 * @param boolean $zoomControl The enabled/disabled state of the zoom control.
	 * @param string $instanceName Javascript instance name to use. Default is "map".
	 */
	public function render(
			// CUSTOM parameters
			$api=NULL,
			$width="450px",
			$height="550px",
			// next is Google Map parameters
			$backgroundColor=NULL,
			$disableDefaultUi=FALSE,
			$disableDoubleClickZoom=TRUE,
			$draggable=TRUE,
			$draggableCursor=NULL,
			$draggingCursor=NULL,
			$keyboardShortcuts=TRUE,
			$mapTypeControl=NULL,
			$maxZoom=NULL,
			$minZoom=NULL,
			$noClear=FALSE,
			$panControl=TRUE,
			$scaleControl=TRUE,
			$scrollWheel=TRUE,
			$streetViewControl=TRUE,
			$zoom=7,
			$zoomControl=TRUE,
			$instanceName='map'
			) {
		if ($api === NULL) {
			$api = "http://maps.google.com/maps/api/js?v=3.2&sensor=true";
		}
		$min = 100000;
		$max = 999999;
		$elementId = 'gm' . rand($min, $max);

		$this->includeFile($api);

		$this->templateVariableContainer->add('layers', array());
		$this->templateVariableContainer->add('infoWindows', array());

		$this->inheritArguments();
		$children = $this->renderChildren();

		$markers = $this->renderMarkers();

		$lat = $this->arguments['lat'] ? $this->arguments['lat'] : 56.25;
		$lng = $this->arguments['lng'] ? $this->arguments['lng'] : 10.45;

		$options = $this->getMapOptions();

		$js = <<< INIT
var markers = [];
var {$instanceName};
var {$instanceName}timeout;
var {$instanceName}refreshList = function() {
	var i;
	var markerlist = jQuery('.fed-maplist');
	for (i=0; i<markers.length; i++) {
		var marker = markers[i];
		var row = markerlist.find('tr.' + marker.get('id'));
		if (map.getBounds().contains(marker.getPosition())) {
			row.removeClass('off');
		} else {
			row.addClass('off');
		}
	};
	if (typeof tableSorter != 'undefined') {
		tableSorter.fnDraw();
	};
};
var {$instanceName}timer = function() {
	clearTimeout({$instanceName});
	{$instanceName}timeout = setTimeout({$instanceName}refreshList, 400);
};

jQuery(document).ready(function() {
	var myLatlng = new google.maps.LatLng({$lat}, {$lng});
	var myOptions = {$options};
	var infoWindow = infoWindow = new google.maps.InfoWindow({maxWidth: 400, maxHeight: 400});
	{$instanceName} = new google.maps.Map(document.getElementById("{$elementId}"), myOptions);
{$markers}
	// check for a map list instance. If found, hook it up to various map events
	var listElement = jQuery('.fed-maplist');
	if (listElement.html() != '') {
		//{$instanceName}refreshList();
		google.maps.event.addListener(map, 'zoom_changed', {$instanceName}timer);
		google.maps.event.addListener(map, 'bounds_changed', {$instanceName}timer);
		google.maps.event.addListener(map, 'center_changed', {$instanceName}timer);
		google.maps.event.addListener(map, 'resize', {$instanceName}timer);
	}
});

INIT;

		$css = <<< CSS
#{$elementId} {
	width: {$width};
	height: {$height};
}
CSS;

		$this->includeHeader($js, 'js');
		$this->includeHeader($css, 'css');

		$this->tag->addAttribute('id', $elementId);
		$this->tag->addAttribute('class', $this->arguments['class']);

		$this->tag->setContent($children);

		return $this->tag->render();
	}

	public function get($name) {
		if ($this->templateVariableContainer->exists($name)) {
			return $this->templateVariableContainer->get($name);
		} else {
			return FALSE;
		}
	}

	public function reassign($name, $value) {
		if ($this->templateVariableContainer->exists($name)) {
			$this->templateVariableContainer->remove($name);
		}
		$this->templateVariableContainer->add($name, $value);
	}

	public function inheritArguments() {
		$config = $this->get('config');
		if ($config === FALSE) {
			$config = array();
		}
		$arguments = $this->getArguments();
		foreach ($arguments as $name=>$value) {
			$config[$name] = $value;
		}
		$this->reassign('config', $config);
		return $config;
	}

	public function getArguments() {
		$args = array();
		$defs = $this->prepareArguments();
		foreach ($defs as $def) {
			$name = $def->getName();
			if ($this->arguments->hasArgument($name)) {
				$args[$name] = $this->arguments[$name];
			}
		}
		return $args;
	}

	public function renderMarkers() {
		$layers = $this->get('layers');
		$allMarkers = array();
		foreach ($layers as $name=>$markers) {
			foreach ($markers as $index=>$marker) {
				$markerId = $marker['id'];
				if ($marker['infoWindow']) {
					$infoWindow = $marker['infoWindow'];
					$infoWindow = str_replace("\n", "\\n", $infoWindow);
					$infoWindow = str_replace('"', '\"', $infoWindow);
					unset($marker['infoWindow']);
				} else {
					$infoWindow = FALSE;
				}
				$options = $this->getMarkerOptions($marker);
				$str = "\tvar {$markerId} = new google.maps.Marker($options); {$markerId}.set('id', '{$markerId}'); markers.push({$markerId}); ";
				if ($infoWindow) {
					$str .= "    google.maps.event.addListener({$markerId}, 'click', function(event) { infoWindow.close(); infoWindow.setOptions({maxWidth: 600}); infoWindow.open(map, {$markerId}); infoWindow.setContent(\"{$infoWindow}\"); });";
				}
				array_push($allMarkers, $str);
			}
		}
		$this->reassign('layers', $layers);
		return implode("\n", $allMarkers);
	}

	public function getOptions($object) {
		$lines = array();
		foreach ($object as $name=>$value) {
			if (is_numeric($value)) {
				// NOOP
			} else if (is_string($value)) {
				$value = "\"{$value}\"";
			} else if (is_null($value)) {
				continue;
			} else if (is_bool($value)) {
				$value = $value ? 'true' : 'false';
			}
			$lines[] = "\"{$name}\":{$value}";
		}
		return $lines;
	}

	public function getMapOptions() {
		$lines = array(
			"center: myLatlng",
        	"mapTypeId: " . $this->arguments['mapTypeId'],
			"size: new google.maps.Size(500,500)"
		);
		$lines = array_merge($lines, $this->getOptions($this->getArguments()));
		return $this->objWrap($lines);
	}

	public function getMarkerOptions($marker) {
		$removables = array(
			"width", "height", "disableDefaultUi", "disableDoubleClickZoom", "draggable",
			"keyboardShortcuts", "mapTypeControl", "noClear", "panControl", "scaleControl",
			"scrollWheel", "streetViewControl", "zoom", "zoomControl", "instanceName", "class",
			"data", "properties"
		);
		$lines = array(
			"position: new google.maps.LatLng({$marker['lat']},{$marker['lng']})",
			"map: map",
			#shadow 	string|MarkerImage 	Shadow image
			#icon 	string|MarkerImage 	Icon for the foreground
			#shape MarkerShape Image map region definition used for drag/click.
		);
		$lines = array_merge($lines, $this->getOptions($marker));
		foreach ($lines as $k=>$v) {
			$key = substr($v, 0, strpos($v, ':'));
			if (in_array($key, $removables)) {
				unset($lines[$k]);
			}
		}
		// now we need to unset the parameters which are only related to Map:

		return $this->objWrap($lines);
	}

	public function objWrap($lines) {
		$str = "{".implode(", ", $lines)."}";
		return $str;
	}



}
?>