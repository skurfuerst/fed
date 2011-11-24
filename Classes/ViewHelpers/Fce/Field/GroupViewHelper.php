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

class Tx_Fed_ViewHelpers_Fce_Field_GroupViewHelper extends Tx_Fed_ViewHelpers_Fce_Field_SelectViewHelper {

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('internalType', 'string', 'FlexForm-internalType of this Group Selector', TRUE);
		$this->registerArgument('allowed', 'string', 'FlexForm-style "allowed" content for a group type field');
		$this->registerArgument('uploadFolder', 'string', 'Upload folder');
	}
	
	/**
	 * Render method
	 */
	public function render() {
		$config = $this->getFieldConfig();
		$config['type'] = 'group';
		$config['internal_type'] = $this->arguments['internalType'];
		$config['allowed'] = $this->arguments['allowed'];
		$config['uploadfolder'] = $this->arguments['uploadFolder'];
		$this->addField($config);
		$this->renderChildren();
	}


}

?>