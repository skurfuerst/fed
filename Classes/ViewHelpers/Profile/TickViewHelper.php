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
 * @subpackage ViewHelpers\Profile
 */
class Tx_Fed_ViewHelpers_Profile_TickViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {


	public function initializeArguments() {
		$this->registerArgument('content', 'boolean', 'If specified, this message is used - otherwise output of renderChildren() is used', FALSE, NULL);
		$this->registerArgument('inline', 'boolean', 'If TRUE, prepends tick details to rendered child elements', FALSE, FALSE);
		$this->registerArgument('duration', 'integer', 'If already measured, use this duration (in whole miliseconds)', FALSE, -1);
	}

	/**
	 * Register a profiler tick with optional message
	 * 
	 * @return string
	 */
	public function render() {
		$content = $this->arguments['content'];
		$inline = $this->arguments['inline'];
		$duration = $this->arguments['duration'];
		$profile =& $GLOBALS['fed_profile'];
		if (is_array($profile) == FALSE) {
			Tx_Fed_ViewHelpers_Profile_ResetViewHelper::render();
		}

		$now = microtime(TRUE);
		$last = $profile['tick'];
		if (!$last) {
			$last = $now;
		}
		if ($duration < 0) {
			$duration = number_format(($now - $last) * 1000, 0);
		}
		if ($content === NULL) {
			$content = $this->renderChildren();
			if (strlen(trim($content)) == 0) {
				$content = "Tick #" . count($profile['ticks']);
			}
		}
		$tick = array(
			'duration' => $duration,
			'content' => $content,
		);


		array_push($profile['ticks'], $tick);
		$profile['tick'] = $now;

		$html = $this->renderChildren();
		if ($inline) {
			$info = "<div>Tick #" . count($profile['ticks']) . " ({$tick['duration']} ms)</div>";
			$html = "{$info}\n{$html}";
		}

		return $html;
	}
}

?>