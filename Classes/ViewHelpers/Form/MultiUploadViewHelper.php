<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ************************************************************* */

/**
 *
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Fce
 */
class Tx_Fed_ViewHelpers_Form_MultiUploadViewHelper extends Tx_Fluid_ViewHelpers_Form_UploadViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'div';

	/**
	 * @var string
	 */
	protected $uniqueId = 'plupload';

	/**
	 * @var string
	 */
	protected $editorId = 'pleditor';

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * @param Tx_Fed_Utility_DocumentHead $documentHead
	 */
	public function injectDocumentHead(Tx_Fed_Utility_DocumentHead $documentHead) {
		$this->documentHead = $documentHead;
	}

	/**
	 * @param Tx_Fed_Utility_JSON $jsonService
	 */
	public function injectJsonService(Tx_Fed_Utility_JSON $jsonService) {
		$this->jsonService = $jsonService;
	}

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('runtimes', 'string', 'CSV list of allowed runtimes - see plupload doc', FALSE, 'html5,flash,gears,silverlight,browserplus,html4');
		$this->registerArgument('url', 'string', 'If specified, overrides built-in uploader with one you created and placed at this URL');
		$this->registerArgument('maxFileSize', 'string', 'Maxium allowed file size', FALSE, '10mb');
		$this->registerArgument('chunkSize', 'string', 'Chunk size when uploading in chunks', FALSE, '1mb');
		$this->registerArgument('uniqueNames', 'boolean', 'If TRUE, obfuscates and randomizes file names. Default behavior is to use TYPO3 unique filename features', FALSE, FALSE);
		$this->registerArgument('resizeWidth', 'integer', 'If set, uses client side resizing of any added images width', FALSE);
		$this->registerArgument('resizeHeight', 'integer', 'If set, uses client side resizing of any added images height', FALSE);
		$this->registerArgument('resizeQuality', 'integer', 'Range 0-100, quality of resized image', FALSE, 90);
		$this->registerArgument('filters', 'array', 'Array label=>csvAllowedExtensions of file types to browse for', FALSE, array('title' => 'All files', 'extensions' => '*'));
		$this->registerArgument('uploadfolder', 'string', 'If specified, uses this site relative path as target upload folder. If a form object exists and this argument is not present, TCA uploadfolder is used as defined in the named field\'s definition');
		$this->registerArgument('preinit', 'array', 'Array of preinit event listener methods - see plupload documentation for reference. The default event which sets the contents of the hidden field is always fired.', FALSE, array());
		$this->registerArgument('init', 'array', 'Array of init event listener methods - see plupload documentation for reference. The default event which sets the contents of the hidden field is always fired.', FALSE, array());
	}

	/**
	 * Renders a multi-upload field using plupload. Posts value as simple string.
	 *
	 * @return string
	 */
	public function render() {
		$name = $this->getName();
		$value = $this->getValue();
		$this->registerFieldNameForFormTokenGeneration($name);
		$this->setErrorClassAttribute();
		$html = array(
			'<input id="' . $this->uniqueId . '-field" type="hidden" name="' . $name . '" value="' . $value . '" />',
			'<div class="fed-plupload plupload_container">',
				'<div id="' . $this->uniqueId . '" class=""></div>',
			'</div>',
			'<div class="fed-upload plupload_container">',
				'<div class="plupload">',
					'<div class="ui-state-default ui-widget-header plupload_header">',
						'<div class="plupload_header_content">',
							'<div class="plupload_header_title">Saved files</div>',
							'<div class="plupload_header_text">You can drag and drop to sort the list</div>',
						'</div>',
					'</div>',
					'<div class="fed-plupload">',
						'<table class="plupload_filelist">',
							'<tbody>',
								'<tr class="ui-widget-header plupload_filelist_header">',
									'<td class="plupload_cell plupload_file_name">Saved files</td>',
									'<td class="plupload_cell plupload_file_status"></td>',
									'<td class="plupload_cell plupload_file_size">Size</td>',
									'<td class="plupload_cell plupload_file_action"></td>',
								'</tr>',
							'</tbody>',
						'</table>',
						'<div class="ui-widget-content plupload_scroll">',
							'<table class="plupload_filelist_content">',
							'<tbody id="' . $this->editorId . '"></tbody>',
							'</table>',
						'</div>',
					'</div>',
				'</div>',
			'</div>',
		);
		$this->tag->setContent(implode(chr(10), $html));
		$this->addScript();
		return $this->tag->render();
	}

	/**
	 * @return string
	 */
	protected function getPreinitEventsJson() {
		return $this->getEventsJson($this->arguments['preinit']);
	}

	/**
	 * @return string
	 */
	protected function getInitEventsJson() {
		$events = $this->arguments['init'];
		$events['FileUploaded'] = "FED.FileListEditor.onFileUploaded";
		return $this->getEventsJson($events);
	}

	/**
	 * @param array $eventsDefinition
	 * @return string
	 */
	protected function getEventsJson($eventsDefinition) {
		$lines = array();
		foreach ($eventsDefinition as $eventName=>$functionName) {
			$definition = $eventName . ': ' . $functionName;
			array_push($lines, $definition);
		}
		if (count($lines) == 0) {
			return '{}';
		} else {
			return '{' . implode(', ', $lines) . '}';
		}
	}

	/**
	 * Adds necessary scripts to header
	 */
	protected function addScript() {

		$scriptPath = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/';
		$pluploadPath = $scriptPath . 'com/plupload/js/';
		$value = $this->getPropertyValue();
		$value = trim($value, ',');
		if (strlen($value) > 0) {
			$existingFiles = explode(',', trim($this->getPropertyValue(), ','));
		} else {
			$existingFiles = array();
		}
		$propertyName = $this->arguments['property'];
		$formObject = $this->viewHelperVariableContainer->get('Tx_Fluid_ViewHelpers_FormViewHelper', 'formObject');
		$uploadFolder = $this->infoService->getUploadFolder($formObject, $propertyName);

		$files = array(
			$scriptPath . 'GearsInit.js',
			$pluploadPath . 'plupload.full.js',
			$pluploadPath . 'jquery.plupload.queue/jquery.plupload.queue.js',
			$pluploadPath . 'jquery.ui.plupload/jquery.ui.plupload.js',
			$scriptPath . 'FileListEditor.js'
		);

		foreach ($files as $file) {
			$contents = file_get_contents($file);
			$this->documentHead->includeHeader($contents, 'js');
		}

		$this->documentHead->includeFiles(array(
			t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css',
			t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css',
		));

		foreach ($existingFiles as $k=>$file) {
			$size = (string) intval(filesize(PATH_site . $uploadFolder . DIRECTORY_SEPARATOR . $file));
			$existingFiles[$k] = "{id: 'f{$k}', name: '{$file}', size: {$size}, percent: 100, completed: {$size}, status: plupload.QUEUED, existing: true}";
		}

		$filesJson = "[" . implode(', ', $existingFiles) . "]";

		if ($this->arguments['resizeWidth'] > 0 || $this->arguments['resizeHeight'] > 0) {
			if ($this->arguments['resizeWidth'] > 0) {
				$resizeWidth = "width : {$this->arguments['resizeWidth']},";
			}
			if ($this->arguments['resizeHeight'] > 0) {
				$resizeHeight = "height : {$this->arguments['resizeHeight']},";
			}
			$resize = "resize : { width: {$resizeWidth}, height: {$resizeHeight}, quality : {$this->arguments['resizeQuality']}},";
		}
		$url = $this->getUrl();
		$preinit = $this->getPreinitEventsJson();
		$init = $this->getInitEventsJson();
		$filterJson = $this->jsonService->encode($this->arguments['filters']);
		$this->documentHead->includeHeader("
jQuery(document).ready(function() {
	jQuery('#{$this->uniqueId}').plupload({
		runtimes : '{$this->arguments['runtimes']}',
		url : '{$url}',
		max_file_size : '{$this->arguments['maxFileSize']}',
		chunk_size : '{$this->arguments['chunkSize']}',
		unique_names : false,
		autostart: true,
		buttons: {
			browse: true,
			start: false,
			stop: false
		},
		filters : {$filterJson},
		flash_swf_url : '{$flashFile}',
		silverlight_xap_url : '{$silverLightFile}',
		{$resize}
		preinit: {$preinit},
		init: {$init}
	});
	FED.FileListEditor.addFileToSavedList({$filesJson});
	jQuery('#{$this->editorId} a.remove').click(FED.FileListEditor.removeFileFromSavedList);
	//jQuery('#plupload_filelist').append(jQuery('#{$this->editorId}').parents('table:first'));

});
", 'js');

		$style = <<< STYLE
.fed-plupload { margin-bottom: 8px; }
.fed-plupload td,
.fed-plupload table { border-spacing: 0px !important; border-collapse: collapse !important; }
.plupload_container { padding: 0px; }
.plupload_header_content { padding-left: 8px; background-image: none !important; }
STYLE;
		$this->documentHead->includeHeader($style, 'css');
	}

	public function getUrl() {
		$formObject = $this->viewHelperVariableContainer->get('Tx_Fluid_ViewHelpers_FormViewHelper', 'formObject');
		$propertyName = $this->arguments['property'];
		if ($this->arguments['url']) {
			$url = $this->arguments['url'];
		} else if ($formObject && $propertyName) {
			$formObjectClass = get_class($formObject);
			$controllerName = $this->controllerContext->getRequest()->getControllerName();
			$pluginName = $this->controllerContext->getRequest()->getPluginName();
			$extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
			$arguments = array(
				'objectType' => $formObjectClass,
				'propertyName' => $propertyName
			);
			$url = $this->controllerContext->getUriBuilder()
				->uriFor('upload', $arguments, $controllerName, $extensionName, $pluginName);
			$url = '/' . $url; // Why, O why, must baseUrl not be respected in JS in browsers?
		} else {
			throw new Tx_Fluid_Exception('Multiupload ViewHelper requires either url argument or associated form object', 1312051527);
		}
		return $url;
	}

}
?>