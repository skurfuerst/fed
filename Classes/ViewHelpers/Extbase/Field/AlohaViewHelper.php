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
 * @subpackage ViewHelpers\Extbase\Field
 */
class Tx_Fed_ViewHelpers_Extbase_Field_AlohaViewHelper extends Tx_Fed_ViewHelpers_FieldViewHelper {
	
	/**
	 * Render the Field
	 * 
	 * @param string $displayType Type (WS JS domain style) of Field 
	 * @param string $name Name property of the Field
	 * @param string $value Value property of the Field
	 * @param string $class Class property of the Field
	 * @param string $type Type (input, hidden, radio, checkbox) of the <input> field
	 * @param string $sanitizer WS JS Domain style reference to validator method
	 * @param string $tag Tagname to use for rendered container
	 * @param string $ruleSelector CSS selector for rule. If specified, needs rule parameter too
	 * @param array $rule Array of rules for elements matching ruleSelector CSS selector parameter
	 * @param string $placeholder Placeholder text if length of current text is zero
	 */
	public function render(
			$displayType='dk.wildside.display.field.Aloha', 
			$name=NULL, 
			$value=NULL, 
			$class=NULL, 
			$type='input', 
			$sanitizer=NULL, 
			$tag='p',
			$ruleSelector=NULL,
			$rule=NULL,
			$placeholder=NULL) {
		$this->includes();
		if (strlen($value) == 0 && $placeholder) {
			$inner = $placeholder;
		} else {
			$inner = $value;
		}
		$field = "<{$tag} class='aloha {$class}'>{$inner}</{$tag}>";
		if ($ruleSelector && $rule) {
			$field .= $this->getRule($ruleSelector, $rule);
		}
		return parent::render($field, $displayType, $name, $value, NULL, $sanitizer);
	}
	
	private function getRule($selector, array $rule=NULL) {
		$json = json_encode($rule);
		if ($GLOBALS['fedAlohaRules'][$selector] == $json) {
			return '';
		}
		$GLOBALS['fedAlohaRules'][$selector] = $json;
		return "<div class='fed-aloha-rule' title='{$selector}'>{$json}</div>";
	}
	
	private function includes() {
		$jsBasePath = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/gentics/aloha/';
		$files = array(
			'aloha.js',
			'plugins/com.gentics.aloha.plugins.Format/plugin.js',
			'plugins/com.gentics.aloha.plugins.Table/plugin.js',
			'plugins/com.gentics.aloha.plugins.List/plugin.js',
			'plugins/com.gentics.aloha.plugins.Link/plugin.js',
			'plugins/com.gentics.aloha.plugins.Link/LinkList.js',
			'plugins/com.gentics.aloha.plugins.Paste/plugin.js',
			'plugins/com.gentics.aloha.plugins.Paste/wordpastehandler.js'
		);
		foreach ($files as $k=>$v) {
			$files[$k] = "{$jsBasePath}{$v}";
		}
		$init = <<< SCRIPT
GENTICS.Aloha.settings = {
	//logLevels: {'error': true, 'warn': true, 'info': false, 'debug': false},
	logLevels : false,
	errorhandling : false,
	ribbon: false,	
	"i18n": { "current": "en" },
	"plugins": {
		"com.gentics.aloha.plugins.Format": {
			config : [ 'b', 'i','u','del','sub','sup', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre'],
			editables : {
				'.noFormatting' : []
			}
		},
	 	"com.gentics.aloha.plugins.List": {
	 		config : [ 'ol', 'ul' ],
			editables : {
				'.noFormatting' : []
			}
		},
		"com.gentics.aloha.plugins.Link": {
			config : [ 'a' ],
			editables : {
				'.noFormatting' : []
			}
			/*,
			onHrefChange: function( obj, href, item ) {
				// Make sure that links are not allowed inside Aloha-objects instantiated
				// on headers. Sadly, the above configuration is not enough to keep the 
				// link button hidden at all times.
				// ... and this code doesn't work. Sigh.
				var p = jQuery(obj).parents('.GENTICS_editable:first').filter(':header');
				if (p.length) obj.remove();
			}
			*/
		},
		"com.gentics.aloha.plugins.Table": {}
	}
};
// Subscribe to the edit-finish event on all existing (and future) Aloha-instances.
GENTICS.Aloha.EventRegistry.subscribe(GENTICS.Aloha, "editableActivated", function(event, eventProperties) {
	jQuery(eventProperties.editable.obj).data("field").beginEdit();
});

GENTICS.Aloha.EventRegistry.subscribe(GENTICS.Aloha, "editableDeactivated", function(event, eventProperties) {
	if (eventProperties.editable.isModified()) {
		jQuery(eventProperties.editable.obj).data("field").endEdit();
		eventProperties.editable.setUnmodified();
	};
});
SCRIPT;
		#$includer = t3lib_div::makeInstance('Tx_Fed_ViewHelpers_Inject_JsViewHelper');
		#$includer->type = Tx_Fed_ViewHelpers_InjectViewHelper::TYPE_JAVASCRIPT;
		$includer->includeFiles($files);
		$includer->render($init);
		return TRUE;
	}
	
}


?>