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
 * @subpackage Resource
 *
 */
class Tx_Fed_Resource_File {

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $extension;

	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @var DateTime
	 */
	protected $modified;

	/**
	 * @var DateTime
	 */
	protected $created;

	/**
	 * @var string
	 */
	protected $absolutePath;

	/**
	 * @var mixed
	 */
	protected $metadata;

	/**
	 * CONSTRUCTOR, takes absolute path to file as only argument
	 *
	 * @param string $filename
	 */
	public function __construct($filename) {
		$this->filename = $filename;
		if (file_exists($filename)) {
			$pathinfo = pathinfo($filename);
			$this->extension = $pathinfo['extension'];
			$this->filename = $pathinfo['filename'];
			$this->path = $pathinfo['dirname'];
			$this->size = filesize($filename);
			$this->created = new DateTime(filectime($filename));
			$this->modified = new DateTime(filemtime($filename));
			$this->absolutePath = $filename;
		}
	}

	/**
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * @param string $filename
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path;
	}

	/**
	 * @return string
	 */
	public function getExtension() {
		return $this->extension;
	}

	/**
	 * @param string $extension
	 */
	public function setExtension($extension) {
		$this->extension = $extension;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @param int $size
	 */
	public function setSize($size) {
		$this->size = $size;
	}

	/**
	 * @return DateTime
	 */
	public function getModified() {
		return $this->modified;
	}

	/**
	 * @param DateTime $modified
	 */
	public function setModified(DateTime $modified) {
		$this->modified = $modified;
	}

	/**
	 * @return DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param DateTime $created
	 */
	public function setCreated(DateTime $created) {
		$this->created = $created;
	}

	/**
	 * @return string
	 */
	public function getAbsolutePath() {
		return $this->absolutePath;
	}

	/**
	 * @param string $absolutePath
	 */
	public function setAbsolutePath($absolutePath) {
		$this->absolutePath = $absolutePath;
	}

	/**
	 * @return mixed
	 */
	public function getMetadata() {
		return $this->metadata;
	}

	/**
	 * @param mixed $metadata
	 */
	public function setMetadata($metadata) {
		$this->metadata = $metadata;
	}


}


?>