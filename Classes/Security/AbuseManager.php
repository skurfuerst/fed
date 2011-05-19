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
 * @subpackage Security
 */
class Tx_Fed_Security_AbuseManager implements t3lib_Singleton {
	
	const sessionKey = 'Tx_Fed_Security_AbuseManager::sessionKey'; 
	
	/**
	 * Start a new SecuritySession and register for monitoring. Chaining available on return value
	 * 
	 * @api
	 * @param string $id Mandatory ID for this particular session (not browser session; security session - multiple security sessions may be registed for each browser session)
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function startSession($id=NULL) {
		if ($id === NULL) {
			$id = md5(time()*microtime(TRUE));
		}
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$session = $objectManager->get('Tx_Fed_Security_SecuritySession', $id);
		return $this->registerSecuritySession($session);
	}
	
	/**
	 * Registers a SecuritySession for monitoring
	 * 
	 * @api
	 * @param Tx_Fed_Security_SecuritySession $session
	 * @return void
	 */
	public function registerSecuritySession(Tx_Fed_Security_SecuritySession $session) {
		$id = $session->getId();
		$storage = $this->getSessionStorage();
		if (isset($storage[$id]) === FALSE) {
			$storage[$id] = $session;
		}
		$this->updateSessionStorage($storage);
		return $session;
	}
	
	/**
	 * Unregister this SecuritySession
	 * 
	 * @api
	 * @param Tx_Fed_Security_SecuritySession $session
	 * @return void
	 */
	public function unregisterSecuritySession(Tx_Fed_Security_SecuritySession $session) {
		$id = $session->getId();
		$storage = $this->getSessionStorage();
		if (isset($storage[$id]) === TRUE) {
			unset($storage[$id]);
		}
		$this->updateSessionStorage($storage);
		return $session;
	}
	
	/**
	 * Get an identified (or session registered) SecuritySession
	 * 
	 * @api
	 * @param string $id
	 * @return Tx_Fed_Security_SecuritySession
	 */
	public function getSecuritySession($id=NULL) {
		$storage = $this->getSessionStorage();
		if (isset($storage[$id]) === TRUE) {
			return $storage[$id];
		} else {
			return NULL;
		}
	}
	
	/**
	 * Get all SecuritySessions for the current browser session
	 * 
	 * @api
	 * @return array
	 */
	public function getAllSecuritySessions() {
		$sessions = $this->getSessionStorage();
		return (array) $sessions;
	}
	
	/**
	 * Patrol all SecuritySessions and trigger redirects if necessary
	 * @api
	 * @return void
	 */
	public function patrol() {
		$sessions = $this->getAllSecuritySessions();
		foreach ($sessions as $session) {
			if ($session->isBlackHoled()) {
				$this->triggerBlackHole();
			} else if ($session->isQuarantined()) {
				$this->triggerQuarantine($session);
				$this->triggerRedirect($session->getQuarantinePage());
			} 
		}
	}
	
	/**
	 * Fetch SecuritySession from argument - autofetch if NULL
	 * 
	 * @param Tx_Fed_Security_SecuritySession $session
	 * @return Tx_Fed_Security_SecuritySession
	 */
	private function fetchSession(Tx_Fed_Security_SecuritySession $session=NULL) {
		if ($session === NULL) {
			// make sure that we return the current session if one exists and no $session argument was specified
			$session = $this->getSecuritySession(NULL);
		}
		return $session;
	}
	
	/**
	 * Trigger a Header Redirect to page $pageUid (using UriBuilder)
	 * 
	 * @param int $pageUid
	 */
	private function triggerRedirect($pageUid) {
		
	}
	
	/**
	 * Put the client into a Black Hole - do whatever we can to lock-up the client without 
	 * putting load on the server
	 */
	private function triggerBlackHole() {
		
	}
	
	/**
	 * Trigger various logging mechanisms when Quarantine is requested by SecuritySession
	 */
	private function triggerQuarantine() {
		
	}
	
	/**
	 * Gets the session storage array
	 * 
	 * @return array
	 */
	private function getSessionStorage() {
		$this->maintainSession();
		$id = $session->getId();
		$storage = $_SESSION[self::sessionKey];
		return $storage;
	}
	
	/**
	 * Update the session storage
	 * 
	 * @param array $storage
	 */
	private function updateSessionStorage(array $storage) {
		$_SESSION[self::sessionKey] = $storage;
	}
	
	/**
	 * Maintains the session storage - starts it if not initialized
	 */
	private function maintainSession() {
		if (is_array($_SESSION[self::sessionKey]) === FALSE) {
			$_SESSION[self::sessionKey] = array();
		}
	}
	
	/**
	 * Process SecuritySessions, redirect if any SecuritySession demands it
	 * 
	 * @return void
	 */
	public function __destruct() {
		$this->patrol();
	}
	
	public function __serialize() {
		
	}
	
	public function __unserialize() {
		
	}
}

?>