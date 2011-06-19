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
 * Flexible Content Element Plugin Rendering Controller
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Controller
 */
class Tx_Fed_Controller_FlexibleContentElementController extends Tx_Fed_Core_AbstractController {

	/**
	 * @var Tx_Fed_Domain_Repository_FceRepository
	 */
	protected $fceRepository;

	/**
	 * @param Tx_Fed_Domain_Repository_FceRepository $fceRepository
	 */
	public function injectFceRepository(Tx_Fed_Domain_Repository_FceRepository $fceRepository) {
		$this->fceRepository = $fceRepository;
	}

	/**
	 * Show template as defined in flexform
	 * @return string
	 */
	public function showAction() {
		$flexform = $this->flexform->getAll();
		$cObj = $this->request->getContentObjectData();
		$uid = $cObj['tx_fed_fceuid'];
		$fce = $this->fceRepository->findByUid($uid);
		$filename = $fce->getFilename();
		$this->view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
		$this->view->setTemplatePathAndFilename(PATH_site . $filename);
		$this->view->assignMultiple($flexform);
		return $this->view->render();
	}

}

?>