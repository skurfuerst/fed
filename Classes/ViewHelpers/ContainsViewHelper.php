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
 *
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_ContainsViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $service
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $service) {
		$this->infoService = $service;
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('needle', 'mixed', 'Needle to search for in haystack', TRUE);
		$this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle', TRUE);
	}

	/**
	 * Render method
	 */
	public function render() {
		$haystack = $this->arguments['haystack'];
		$needle = $this->arguments['needle'];

		if (is_array($haystack)) {
			$evaluation = $this->assertHaystackIsArrayAndHasNeedle($haystack, $needle);
		} else if (is_string($haystack)) {
			$evaluation = $this->assertHaystackIsStringAndHasNeedle($haystack, $needle);
		} else if ($haystack instanceof Tx_Extbase_Persistence_QueryResultInterface) {
			$evaluation = $this->assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
		} else if ($haystack instanceof Tx_Extbase_Persistence_ObjectStorage) {
			$evaluation = $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} else if ($haystack instanceof Tx_Extbase_Persistence_LazyObjectStorage) {
			$evaluation = $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		}

		if ($evaluation === TRUE) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	protected function assertHaystackIsQueryResultAndHasNeedle($haystack, $needle) {
		if ($needle instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
			$needle = $needle->getUid();
		}
		foreach ($haystack as $candidate) {
			if ($candidate->getUid() == $needle) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	protected function assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle) {
		if ($needle instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
			return $haystack->contains($needle);
		} else {
			foreach ($haystack as $candidate) {
				if ($candidate->getUid() === $needle) {
					return TRUE;
				}
			}
			return FALSE;
		}
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	protected function assertHaystackIsArrayAndHasNeedle($haystack, $needle) {
		return in_array($needle, $haystack);
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	protected function assertHaystackIsStringAndHasNeedle($haystack, $needle) {
		return (strpos($haystack, $needle) !== FALSE);
	}

}


?>
