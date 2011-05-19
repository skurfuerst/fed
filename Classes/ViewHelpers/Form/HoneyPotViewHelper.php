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
 * @subpackage ViewHelpers\Form
 */
class Tx_Fed_ViewHelpers_Form_HoneyPotViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {
	
	
	/**
	 * Render a spamprotection honeypot field. The field will be hidden and contain
	 * no text - it will be named with a variation of the name "email" which should
	 * fool any spam robot into filling out the field. A hook is registered which checks 
	 * a stored session variable against every form submit (method=POST); if a 
	 * capture has been detected various log events are added and the session is, 
	 * optionally, shut down completely to prevent any further traffic.
	 * 
	 * You may configure the HoneyPot to display a special page to the user if caught.
	 * 
	 * All notifications to RIPE are recorded in the syslog of TYPO3 and checks are performed
	 * to always avoid repeating abuse reports as long as the syslog has retention.
	 * 
	 * This ViewHelper uses the AbuseAssistant singleton - you can use this yourself
	 * to record what you consider abuse and perform the exact same actions this ViewHelper 
	 * performs, as configured. Just a quick thought, this can be used to limit the amount
	 * of times a user is allowed to perform quarantining, for example of users posting 
	 * too many comments or even using bad language in a comment...
	 * 
	 * See Classes/Security/AbuseAssistant for more information.
	 * 
	 * @param string $id Unique ID string of this particular HoneyPot instance. Can be shared between forms - but would in this case make all forms employing the HoneyPot susceptible to the same abuse/quarantine checks and all calls to the viewhelper overrides previous settings
	 * @param string $name Optional name of the input field generated
	 * @param int $abuseTolerance Number of times a client is allowed to "abuse" the form before being reported
	 * @param int $abusePage UID of page to display when abuse is detected
	 * @param boolean $quarantine If TRUE, client is quarantined after having abused > $abuseTolerance
	 * @param boolean $quarantineIpAddress If TRUE, quarantine is determined by IP address (default=FALSE)
	 * @param boolean $quarantineSession If TRUE, quarantine is limited to session duration (default=FALSE)
	 * @param boolean $quarantinePage If set, displays this page to a qurantined client/ip. If $abusePage is set but $quarantinePage is not, $abusePage is used
	 * @param int $quarantineDuration Number of seconds to quarantine client. Default is 24 hours.
	 * @param boolean $blackHole If TRUE, abusers are sent to a black hole of infinite wait/redirect/wait requests to (hopefully) block the spam bot (default=FALSE)
	 * @param boolean $syslog If TRUE, abuse is logged to TYPO3 syslog (default=TRUE)
	 * @return string
	 */
	public function render(
			$id='honeypot', 
			$name=NULL, 
			$abuseTolerance=0, 
			$abusePage=-1, 
			$syslog=TRUE
		) {
		#
		#$GLOBALS['wildide_extbase_profile'] = array();
		#$GLOBALS['wildide_extbase_profile']['tick'] = microtime(TRUE);
		#$GLOBALS['wildide_extbase_profile']['ticks'] = array();
		return $this->renderChildren();
	}
}

?>