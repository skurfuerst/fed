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
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Resource
 *
 */
class Tx_Fed_ViewHelpers_Resource_ImagesViewHelper extends Tx_Fed_ViewHelpers_Resource_FilesViewHelper {


	/**
	 * Initialize arguments relevant for image-type resource ViewHelpers
	 */
	public function initializeArguments() {
		$this->registerArgument('exif', 'boolean', 'Read exif metadata', FALSE, FALSE);
		$this->registerArgument('resolution', 'boolean', 'Read resolution metadata', FALSE, TRUE);
	}

	/**
	 * Render / process
	 *
	 * @return string
	 */
	public function render() {
		// if no "as" argument and no child content, return linked list of files
		// else, assign variable "as"
		$pathinfo = pathinfo($this->arguments['path']);
		if ($pathinfo['filename'] === '*') {
			$files = $this->documentHead->getFilenamesOfType($pathinfo['dirname'], $pathinfo['extension']);
		}
		$files = $this->arrayToFileObjects($files);
		if ($this->arguments['exif'] === TRUE) {
			$files = $this->applyExifData($files);
		}
		if ($this->arguments['resolution'] === TRUE) {
			$files = $this->applyResolutionData($files);
		}

		// rendering
		$content = "";
		if ($this->arguments['as']) {
			$this->templateVariableContainer->add($this->arguments['as'], $files);
		} else {
			$this->templateVariableContainer->add('images', $files);
			$content = $this->renderChildren();
			$this->templateVariableContainer->remove('images');
		}
		// possible return: HTML file list
		if (strlen(trim($content)) === 0) {
			return $this->renderFileList($files);
		}
	}

	/**
	 * Adds support for sorting on new extended sort properties "size" and "exif"
	 * @param type $src
	 * @return type
	 */
	protected function getSortValue($src) {
		$field = $this->arguments['sortBy'];
		list ($field, $subfield) = explode(':', $field);
		switch ($field) {
			case 'size':
				if (is_file(PATH_site . $src) === FALSE) {
					return 0;
				}
				list ($w, $h) = getimagesize(PATH_site . $src);
				switch ($subfield) {
					case 'w': return $w;
					case 'h': return $h;
					default: return ($w*$h);
				}
			case 'exif': return $this->readExifInfoField(PATH_site . $src, $subfield);
			default: return parent::getSortValue($src);
		}
	}

	/**
	 * Applies resolution information to metadata for all $images
	 *
	 * @param array $images
	 */
	protected function applyResolutionData(array $images) {
		foreach ($images as $k=>$image) {
			$metadata = (array) $image->getMetadata();
			$metadata['resolution'] = getimagesize($image->getAbsolutePath());
			$images[$k]->setMetadata($metadata);
		}
		return $images;
	}

	/**
	 * Applies EXIF information to metadata for all $images
	 *
	 * @param array $images
	 */
	protected function applyExifData(array $images) {
		return $images;
	}

}

?>
