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
 * Content Rotation container. Rotates (shifts w/animation) between any type
 * of content elements placed inside the container. The first-level children
 * are used for content rotation blocks and you can use divs, tables, images etc.
 * - in short, anything you like can be rotated or even continously animated.
 *
 * Examples:
 *
 *
 *
 * Title is required for each tab but is not a required property since it is
 * not needed for the parent element - you must add the title manually for tabs.
 *
 * Note that the same ViewHelpers acts as accordion group and tab renderer. The
 * top-level tag is considered group and the following tabs are considered
 * inidividual tabs. At this time nested accordions are not supported.
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers\JQuery
 * @uses jQuery
 */
class Tx_Fed_ViewHelpers_JQuery_ContentRotatorViewHelper extends Tx_Fed_Core_ViewHelper_AbstractJQueryViewHelper {

	protected $tagName = 'div';

	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('tagName', 'string', 'Tagname to use - table, div, ul, etc. Defaults to div');
		$this->registerArgument('' , '', '');
		$this->registerArgument('' , '', '');
		$this->registerArgument('' , '', '');
		$this->registerArgument('' , '', '');
		$this->registerArgument('' , '', '');
		$this->registerArgument('' , '', '');
		$this->registerArgument('' , '', '');
		$this->registerArgument('' , '', '');
		$this->registerArgument('' , '', '');
	}

}

?>
