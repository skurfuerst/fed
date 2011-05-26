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
 * Accordion integration for jQuery UI - remember to load jQueryUI yourself
 * For example through <ws:script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js" />
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\JQuery
 * @uses jQuery
 */
class Tx_Fed_ViewHelpers_JQuery_AccordionViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	protected $tagName = 'div';

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		parent::initializeArguments();
	}

	/**
	 * Render method
	 * @param string $tagName Tagname to use - default DIV
	 * @param string $animated String name of animation effect to use; FALSE to disable
	 * @param string $active The initially active accordion tag (jQuery selector)
	 * @param boolean $disabled If TRUE, deactivates the accordion
	 * @param boolean $autoHeight If TRUE, automatically adjusts the height of the Accordion
	 * @param boolean $clearStyle If TRUE, clears style of elements touched
	 * @param boolean $fillSpace If TRUE, fills space inside accordion tabs to match height
	 * @return string
	 */
	public function render($tagName='div', $animated='slide', $active='> :first-child', $disabled=FALSE, $autoHeight=FALSE, $clearStyle=FALSE, $fillSpace=FALSE) {

		$this->addScript();
		$this->addClassAttribute();

		$content = $this->renderChildren();

		$this->tag->setContent($content);

		return $this->tag->render();
	}

	/**
	 * Inject an additional classname in tag attributes
	 * @return void
	 */
	private function addClassAttribute() {
		if ($this->arguments['class']) {
			$classes = explode(' ', $this->arguments['class']);
		} else {
			$classes = array();
		}
		array_push($classes, 'fed-accordion');
		$classNames = implode(' ', $classes);
		$this->tag->addAttribute('class', $classNames);
	}

	/**
	 * Attach scripts to header
	 *
	 * @return void
	 */
	private function addScript() {
		$scriptFile = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/jquery/plugins/jquery.accordion.js';
		$disabled = ($this->arguments['disabled'] === TRUE ? 'true' : 'false');
		$autoHeight = ($this->arguments['autoHeight'] === TRUE ? 'true' : 'false');
		$clearStyle = ($this->arguments['clearStyle'] === TRUE ? 'true' : 'false');
		$collapsible = ($this->arguments['collapsible'] === TRUE ? 'true' : 'false');
		$fillSpace = ($this->arguments['fillSpace'] === TRUE ? 'true' : 'false');
		$init = <<< INITSCRIPT
jQuery(document).ready(function() {
	var options = {
		disabled : {$disabled},
		animated : '{$this->arguments['animated']}',
		autoHeight : {$autoHeight},
		clearStyle : {$clearStyle},
		collapsible : {$collapsible},
		fillSpace : {$fillSpace},
		header : '> :first-child',
		active : ''
	};
	jQuery('.fed-accordion').accordion(options);
});
INITSCRIPT;
		$this->includeFile($scriptFile);
		$this->includeHeader($init, 'js');
	}

}


?>