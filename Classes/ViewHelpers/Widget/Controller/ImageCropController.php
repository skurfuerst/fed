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
 ***************************************************************/

/**
 * Image Crop Widget Controller
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Widget
 */
class Tx_Fed_ViewHelpers_Widget_Controller_ImageCropController extends Tx_Fluid_Core_Widget_AbstractWidgetController {

	/**
	 * Initialize action
	 */
	public function initializeAction() {
	}

	/**
	 * @return string
	 */
	public function indexAction() {
		$transferArguments = array(
			'id', 'url',
			'path', 'src', 'placeholderImage', 'placeholderText',
			'uploader', 'largeWidth', 'largeHeight', 'preview', 'previewWidth', 'previewHeight',
			'maxWidth', 'maxHeight', 'aspectRatio',
			'cropButtonLabel', 'sections'
		);
		foreach ($transferArguments as $argumentName) {
			$this->view->assign($argumentName, $this->widgetConfiguration[$argumentName]);
		}
		return $this->view->render();
	}

	/**
	 * Crop $imageFile according to $cropData
	 *
	 * @param string $imageFile
	 * @param array $cropData
	 * @return string
	 */
	public function cropAction($imageFile, array $cropData) {
		$filename = PATH_site . $imageFile;
		$pathinfo = pathinfo($filename);
		$filenameCropped = $pathinfo['dirname'] . '/' . basename($filename);
		if (strtolower($pathinfo['extension']) == 'png') {
			$im = imagecreatefrompng($filename);
		} else {
			$im = imagecreatefromstring(file_get_contents($filename));
		}
		if ($im) {
			$maximumWidth = $this->widgetConfiguration['maxWidth'];
			if ($cropData['w'] > $maximumWidth) {
				$ratio = $maximumWidth / $cropData['w'];
			} else {
				$ratio = 1;
			}
			$cropped = imagecreatetruecolor($cropData['w'] * $ratio, $cropData['h'] * $ratio);
			imagecopyresampled($cropped, $im, 0, 0, $cropData['x'], $cropData['y'], $cropData['w'] * $ratio, $cropData['h'] * $ratio, $cropData['w'], $cropData['h']);
			switch (strtolower($pathinfo['extension'])) {
				case 'gif':
					imagegif($cropped, $filenameCropped);
					break;
				case 'png':
					imagepng($cropped, $filenameCropped);
					break;
				default:
					imagejpeg($cropped, $filenameCropped);
			}
			return basename($filenameCropped);
		} else {
			return '0';
		}
	}

}

?>