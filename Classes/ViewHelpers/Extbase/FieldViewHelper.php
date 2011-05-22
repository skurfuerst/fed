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
 * @subpackage ViewHelpers\Extbase
 */
class Tx_Fed_ViewHelpers_Extbase_FieldViewHelper extends Tx_Fed_Core_ViewHelper_AbstractExtbaseViewHelper {

	public function initializeArguments() {
		#parent::initializeArguments();
		$this->registerArgument('property', 'string', 'Name of property on parent DomainObject this Field relates to', FALSE);
		$this->registerArgument('value', 'string', 'Value of this field', FALSE);
		$this->registerArgument('name', 'string', 'Name of this field', FALSE);
		$this->registerArgument('sanitizer', 'string', 'FED JS Domain style reference to validator method', FALSE, 'noop');
	}

	public function renderChildren($innerHTML=NULL) {
		$json = array(
			'displayType' => $this->arguments['displayType'],
			'name' => 	$this->getFieldName(),
			'value' => $this->getFieldValue(),
			'sanitizer' => $this->arguments['sanitizer'],
		);
		$json = array_merge((array) $this->arguments['config'], $json);
		$jsonString = $this->jsonService->encode($json);
		$html = "<span class='fed-field'>" . chr(10);
		$html .= "<div class='fed-json'>{$jsonString}</div>" . chr(10);
		if ($innerHTML) {
			$html .= $innerHTML . chr(10);
		} else {
			$html .= parent::renderChildren() . chr(10);
		}
		$html .= "</span>" . chr(10);
		return $html;
	}

	public function getObject() {
		return $this->templateVariableContainer->get('object');
	}

	public function getFieldClass() {
		return $this->arguments['class'];
	}

	public function getFieldName() {
		if ($this->arguments->hasArgument('name')) {
			return $this->arguments['name'];
		} else if ($this->arguments->hasArgument('property')) {
			return $this->arguments['property'];
		} else {
			return NULL;
		}
	}

	public function getFieldValue() {
		if ($this->arguments->hasArgument('value')) {
			return $this->arguments['value'];
		} else if ($this->arguments->hasArgument('property')) {
			$object = $this->getObject();
			if (!$object) {
				throw new Exception('Property defined but no object exists in template variable container - did you forget "object" it in your widget arguments?');
			}
			$getter = "get" . ucfirst($this->arguments['property']);
			return $object->$getter();
		} else {
			return;
		}
	}

}


?>