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
 * @subpackage ViewHelpers/Fce/Field
 */

class Tx_Fed_ViewHelpers_Fce_Field_SelectViewHelper extends Tx_Fed_ViewHelpers_Fce_FieldViewHelper {

	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('validate', 'string', 'FlexForm-type validation configuration for this input', FALSE, 'trim');
		$this->registerArgument('items', 'array', 'Items for the selector multidimensional, matching FlexForm/TCA', FALSE, array());
		$this->registerArgument('size', 'integer', 'Size of the selector box', FALSE, 1);
		$this->registerArgument('multiple', 'boolean', 'If TRUE, allows multiple selections', FALSE, FALSE);
		$this->registerArgument('minItems', 'integer', 'Minimum required number of items to be selected', FALSE, 0);
		$this->registerArgument('maxItems', 'integer', 'Maxium allowed number of items to be selected', FALSE, 1);
		$this->registerArgument('table', 'string', 'Define foreign table name to turn selector into a record selector for that table', FALSE, NULL);
		$this->registerArgument('condition', 'string', 'Condition to use when selecting from "foreignTable", supports FlexForm "foregin_table_where" markers', FALSE, NULL);
		$this->registerArgument('mm', 'string', 'Optional name of MM table to use for record selection', FALSE, NULL);
		$this->registerArgument('showThumbs', 'boolean', 'If TRUE, adds thumbnail display when editing in BE', FALSE, TRUE);
		$this->registerArgument('itemsProcFunc', 'string', 'Optional class name of data provider to fill select options');
		$this->registerArgument('requestUpdate', 'boolean', 'If TRUE, the form is force-saved and reloaded when field value changes', FALSE, NULL);
	}

	public function render() {
		$config = $this->getFieldConfig();
		return $config;
		#$this->addField($config);
		#$this->renderChildren();
	}

	protected function getFieldConfig() {
		$config = $this->getBaseConfig();
		$config['type'] = 'select';
		$config['items'] = $this->arguments['items'];
		$config['size'] = $this->arguments['size'];
		$config['minItems'] = $this->arguments['minItems'];
		$config['maxItems'] = $this->arguments['maxItems'];
		$config['multiple'] = $this->arguments['multiple'] ? 1 : 0;
		$config['table'] = $this->arguments['table'];
		$config['condition'] = $this->arguments['condition'];
		$config['mm'] = $this->arguments['mm'];
		$config['showThumbs'] = $this->getFlexFormBoolean($this->argumetns['showThumbs']);
		$config['itemsProcFunc'] = $this->arguments['itemsProcFunc'];
		$config['requestUpdate'] = $this->arguments['requestUpdate'];
		return $config;
	}


}

?>