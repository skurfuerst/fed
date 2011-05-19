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
 * Contains configuration of one instance of an imposed security measure.
 * Instances are constructed and registered with the AbuseManager singleton
 * 
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Security
 */

class Tx_Fed_Security_SecuritySession {
	
	/**
	 * @var string
	 */
	protected $id;
	
	/**
	 * @var int
	 */
	protected $tolerance = 0;
	
	/**
	 * @var int
	 */
	protected $gracePeriod = 300;
	
	/**
	 * @var int
	 */
	protected $abusePage = -1;
	
	/**
	 * @var boolean
	 */
	protected $abuseCountGlobal = TRUE;
	
	/**
	 * @var boolean
	 */
	protected $quarantine = FALSE;
	
	/**
	 * @var boolean
	 */
	protected $quarantined = FALSE;
	
	/**
	 * @var boolean
	 */
	protected $quarantineIpAddress = FALSE;
	
	/**
	 * @var boolean
	 */
	protected $quarantineSession = FALSE;
	
	/**
	 * @var int
	 */
	protected $quarantinePage = -1;
	
	/**
	 * @var int
	 */
	protected $quarantineDuration = 86400;
	
	/**
	 * @var boolean
	 */
	protected $blackHole = FALSE;
	
	/**
	 * @var boolean
	 */
	protected $syslog = TRUE;
	
	/**
	 * @var array
	 */
	private $_abuseLog = array();
	
	/**
	 * CONSTRUCTOR
	 * 
	 * @param string $id Optional ID for SecuritySession
	 */
	public function __construct($id=NULL) {
		if ($id) {
			$this->setId($id);
		} else {
			$id = md5(time()*microtime(TRUE));
			$this->setId($id);
		}
	}
	
	/**
	 * @param string $id
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param int $tolerance
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setTolerance($tolerance) {
		$this->tolerance = $tolerance;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getTolerance() {
		return $this->tolerance;
	}
	
	/**
	 * @param int $gracePeriod
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setGracePeriod($gracePeriod) {
		$this->gracePeriod = $gracePeriod;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getGracePeriod() {
		return $this->gracePeriod;
	}
	
	/**
	 * @param int $abusePage
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setAbusePage($abusePage) {
		$this->abusePage = $abusePage;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getAbusePage() {
		return $this->abusePage;
	}
	
	/**
	 * @param int $abuseCountGlobal
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setAbuseCountGlobal($abuseCountGlobal) {
		$this->abuseCountGlobal = $abuseCountGlobal;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getAbuseCountGlobal() {
		return $this->abuseCountGlobal;
	}
	
	/**
	 * @param boolean $quarantine
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setQuarantine($quarantine) {
		$this->quarantine = $quarantine;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getQuarantine() {
		return $this->quarantine;
	}
	
	/**
	 * @param boolean $quarantined
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setQuarantined($quarantined) {
		$this->quarantined = $quarantined;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getQuarantined() {
		return $this->quarantined;
	}
	
	/**
	 * @param boolean $quarantineIpAddress
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setQuarantineIpAddress($quarantineIpAddress) {
		$this->quarantineIpAddress = $quarantineIpAddress;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getQuarantineIpAddress() {
		return $this->quarantineIpAddress;
	}
	
	/**
	 * @param int $quarantinePage
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setQuarantinePage($quarantinePage) {
		$this->quarantinePage = $quarantinePage;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getQuarantinePage() {
		return $this->quarantinePage;
	}
	
	/**
	 * @param int $quarantineDuration
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setQuarantineDuration($quarantineDuration) {
		$this->quarantineDuration = $quarantineDuration;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getQuarantineDuration() {
		return $this->quarantineDuration;
	}
	
	/**
	 * @param boolean $blackHole
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setBlackHole($blackHole) {
		$this->blackHole = $blackHole;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getBlackHole() {
		return $this->blackHole;
	}
	
	/**
	 * @param boolean $syslog
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function setSyslog($syslog) {
		$this->syslog = $syslog;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getSyslog() {
		return $this->syslog;
	}
	
	/**
	 * Return the number of times abuse of type $abuseType has occurred.
	 * If abuseCountGlobal is set to TRUE and $abuseType is NULL then
	 * the total number of abuse instances recorded is returned
	 * 
	 * @api
	 * @param string $abuseType If specified, returns count for particular type of abuse. Otherwise global.
	 * @return int
	 */
	public function getAbuseCount($abuseType=NULL) {
		if ($abuseType === NULL) {
			$abuseType = 'Tx_Fed_Security_SecuritySession::abuseType';
			if ($this->abuseCountGlobal) {
				return array_sum($this->_abuseLog);
			}
		}
		return intval($this->_abuseLog[$abuseType]);
	}	
	
	/**
	 * Checks if the current session is tainted by abuse of optional type $abuseType
	 * 
	 * @api
	 * @param string $abuseType The type of abuse which occurred. If specified, a local-to-type abuse counter is checked; otherwise the global counter is checked
	 * @return boolean
	 */
	public function isAbuser($abuseType=NULL) {
		return ($this->getAbuseCount($abuseType) > $this->tolerance);
	}
	
	/**
	 * Register an abuse occurrence (of optional specific type) 
	 * 
	 * @param string $abuseType
	 */
	public function registerAbuse($abuseType=NULL) {
		if ($abuseType === NULL) {
			$abuseType = 'Tx_Fed_Security_SecuritySession::abuseType';
		}
		$this->_abuseLog[$abuseType]++;
	}
	
	/**
	 * Check if the session is quarantined
	 * 
	 * @api
	 * @return booelan
	 */
	public function isQuarantined() {
		return FALSE;
	}
	
	/**
	 * No, this does not detect if your user has a blackened rectum.
	 * 
	 * @api
	 * @return boolean
	 */
	public function isBlackHoled() {
		return FALSE;
	}
	
	/**
	 * Place session under quarantine
	 * 
	 * @api
	 * @param int $duration If specified, quarantine is in effect for this many seconds
	 * @return void
	 */
	public function effectQuarantine($duration=-1) {
		if ($duration < 0) {
			$duration = $this->quarantineDuration;
		}
		$this->setQuarantined(TRUE);
	}
	
	/**
	 * Lift quarantine on session
	 * 
	 * @api
	 * @return Tx_Fed_Security_SecuritySession $session
	 */
	public function liftQuarantine() {
		$this->setQuarantined(FALSE);
		return $this;
	}
	
	
}

?>