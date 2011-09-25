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
class Tx_Fed_ViewHelpers_Profile_MeasureViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	public function initializeArguments() {
		$this->registerArgument('inline', 'boolean', 'If TRUE, the child node content is prepended with a short summary - if FALSE, a profile tick is stored with the measured info');
		$this->registerArgument('label', 'string', 'Specify this to identify this particular Measurement');
	}

	/**
	 * Measures rendering time, memory usage and output time of child elements
	 *
	 * @return string
	 */
	public function render() {
		$inline = $this->arguments['inline'];
		$label = $this->arguments['label'];

		$now = microtime(TRUE);
		$mem = memory_get_usage();

		$content = $this->renderChildren();

		$stop = microtime(TRUE);
		$memAfter = memory_get_usage();
		$length = strlen($content);

		$duration = number_format(($stop - $now) * 1000, 0);
		$memUsed = number_format(($memAfter - $mem) / 1024, 2, '.', ',');
		$size = number_format($length / 1024, 2, '.', ',');

		$summary = "{$label}: {$duration} ms, {$size} KB content, {$memUsed} KB memory consumed.";

		if ($inline) {
			$info = "<div>{$summary}</div>";
			$content = "{$info}\n{$content}";
		}
		Tx_Fed_ViewHelpers_Profile_TickViewHelper::render($summary, $inline, $duration);

		return $content;
	}
}

?>