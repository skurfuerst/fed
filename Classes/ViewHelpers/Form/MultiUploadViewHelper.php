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
	protected $uniqueId;

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
		$this->registerArgument('runtimes', 'string', 'CSV list of allowed runtimes - see plupload doc', FALSE, 'gears,flash,silverlight,browserplus,html5');
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
		$this->uniqueId = uniqid('plupload');
		$name = $this->getName();
		$value = $this->getValue();
		$this->registerFieldNameForFormTokenGeneration($name);
		$this->setErrorClassAttribute();
		$html = array(
			'<input id="' . $this->uniqueId . '-field" type="hidden" name="' . $name . '" value="' . $value . '" />',
			'<div id="' . $this->uniqueId . '" class="fed-plupload"></div>'
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
		$events['UploadFile'] = <<< SCRIPT
function(up, file, info) {
	var field = jQuery('#{$this->uniqueId}-field');
	var existing = field.val();
	var arr;
	if (existing != '') {
		arr = existing.split(',');
	} else {
		arr = [];
	};
	arr.push(file.name);
	field.val(arr.join(','));
}
SCRIPT;
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

		$scriptFiles = array(
			'http://bp.yahooapis.com/2.4.21/browserplus-min.js',
			t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/gears_init.js',
			t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/plupload.full.min.js',
			t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/jquery.plupload.queue/jquery.plupload.queue.js',
			t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/jquery.ui.plupload/jquery.ui.plupload.min.js',
		);

		$flashFile = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/plupload.flash.swf';
		$silverLightFile = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/plupload.silverlight.xap';
		if ($this->arguments['resizeWidth'] > 0 || $this->arguments['resizeHeight'] > 0) {
			if ($this->arguments['resizeWidth'] > 0) {
				$resizeWidth = "width : {$this->arguments['resizeWidth']},";
			}
			if ($this->arguments['resizeHeight'] > 0) {
				$resizeHeight = "height : {$this->arguments['resizeHeight']},";
			}
			$resize = <<< RESIZE
		resize : {
			{$resizeWidth}
			{$resizeHeight}
			quality : {$this->arguments['resizeQuality']}
		},
RESIZE;
		}
		$filterJson = $this->jsonService->encode($this->arguments['filters']);
		$filters = <<< FILTERS
		filters : {$filterJson},
FILTERS;

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
			$url = '/' . $url; // Why, O why, must baseUrl not be respected in browsers?
		} else {
			throw new Tx_Fluid_Exception('Multiupload ViewHelper requires either url argument or associated form object', 1312051527);
		}

		$preinit = $this->getPreinitEventsJson();
		$init = $this->getInitEventsJson();
		$script = <<< SCRIPT
jQuery(document).ready(function() {
    jQuery("#{$this->uniqueId}").plupload({
        runtimes : '{$this->arguments['runtimes']}',
        url : '{$url}',
        max_file_size : '{$this->arguments['maxFileSize']}',
        chunk_size : '{$this->arguments['chunkSize']}',
        unique_names : true,
        {$resize}
		{$filter2s}
        flash_swf_url : '{$flashFile}',
        silverlight_xap_url : '{$silverLightFile}',
		preinit: {$preinit},
		init: {$init}
    });
});
SCRIPT;
		$style = <<< STYLE
.fed-plupload { height: 330px; position: relative; }
.fed-plupload td,
.fed-plupload table { border-spacing: 2px !important; border-collapse: collapse !important; }
STYLE;
		$styleFile = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/plupload/jquery.ui.plupload/css/jquery.ui.plupload.css';
		$this->documentHead->includeFiles($scriptFiles);
		$this->documentHead->includeFile($styleFile);
		$this->documentHead->includeHeader($script, 'js');
		$this->documentHead->includeHeader($style, 'css');
	}

}
?>