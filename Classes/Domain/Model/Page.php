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
 * Domain Model for Pages
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Domain\Model
 */
class Tx_Fed_Domain_Model_Page extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var string
	 * @ExtJS
	 * @validate NotEmpty
	 * @validate Text
	 */
	protected $title;

	/**
	 * @var type
	 * @ExtJS
	 * @validate Text
	 */
	protected $subtitle;

	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Fed_Domain_Model_Page>
	 * @lazy
	 */
	protected $subpages;

	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Fed_Domain_Model_ContentElement>
	 * @lazy
	 */
	protected $contentElements;

	/**
	 * @var Tx_Fed_Domain_Model_BackendLayout
	 */
	protected $backendLayout;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->subpages = t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
		$this->contentElements = t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getSubtitle() {
		return $this->subtitle;
	}

	/**
	 * @param string $subtitle
	 */
	public function setSubtitle($subtitle) {
		$this->subtitle = $subtitle;
	}

	/**
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_Fed_Domain_Model_Page> $subpages
	 */
	public function setSubpages(Tx_Extbase_Persistence_ObjectStorage $subpages) {
		$this->subpages = $subpages;
	}

	/**
	 * @param Tx_Fed_Domain_Model_Page $page
	 */
	public function addPage(Tx_Fed_Domain_Model_Page $page) {
		$this->pages->attach($page);
	}

	/**
	 * @param Tx_Fed_Domain_Model_Page $page
	 */
	public function removePage(Tx_Fed_Domain_Model_Page $page) {
		$this->pages->detach($page);
	}

	/**
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_Fed_Domain_Model_ContentElement> $contentElements
	 */
	public function setContentElements(Tx_Extbase_Persistence_ObjectStorage $contentElements) {
		$this->contentElements = $contentElements;
	}

	/**
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Fed_Domain_Model_ContentElement>
	 */
	public function getContentElements() {
		return $this->contentElements;
	}

	/**
	 * @param Tx_Fed_Domain_Model_ContentElement $contentElement
	 */
	public function addContentElement(Tx_Fed_Domain_Model_ContentElement $contentElement) {
		$this->contentElements->attach($contentElement);
	}

	/**
	 * @param Tx_Fed_Domain_Model_ContentElement $contentElement
	 */
	public function removeContentElement(Tx_Fed_Domain_Model_ContentElement $contentElement) {
		$this->contentElements->detach($contentElement);
	}

	/**
	 * @return Tx_Fed_Domain_Model_BackendLayout
	 */
	public function getBackendLayout() {
		return $this->backendLayout;
	}

	/**
	 * @param Tx_Fed_Domain_Model_BackendLayout $backendLayout
	 */
	public function setBackendLayout(Tx_Fed_Domain_Model_BackendLayout $backendLayout) {
		$this->backendLayout = $backendLayout;
	}

}

?>