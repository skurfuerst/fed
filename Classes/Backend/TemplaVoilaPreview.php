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
*  the Free Software Foundation; either version 3 of the License, or
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
 * Flexible Content Element Backend Renderer - TemplaVoila Proxy
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Backend
 */

class Tx_Fed_Backend_TemplaVoilaPreview extends tx_templavoila_preview_default {

	/**
	 *
	 * @param array $row
	 * @param string $table
	 * @param string $output
	 * @param boolean $alreadyRendered
	 * @param object $ref
	 * @return string
	 */
	public function render_previewContent ($row, $table, $output, $alreadyRendered, &$ref) {
		$content = "";
		$drawItem = TRUE;
		$realPreviewer = t3lib_div::makeInstance('Tx_Fed_Backend_Preview');
		$realPreviewer->preProcessFlexibleContentElement($drawItem, $content, $row);
		return $content;
	}
}

?>