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
 * DISCLAIMER:
 * 
 * PLEASE RESPECT YOU USERS! - do not abuse this feature! Its ONLY intended use
 * is for UDP-P2P-type applications or support for specialized client-applications
 * which need your TYPO3-webapplication to "talk back" directly to the user without
 * employing interval-based HTTP requests but with the need to support usage 
 * behind restrictive (corporate usually) firewalls. Yeah, I know. Special case.
 * 
 * This fairly simple class is capable of punching through a NAT on the 
 * client side, through any security layer which is configured to still
 * allow sending of UDP packets. Which would be any system, pretty much.
 * 
 * Blends very well with advanced Flash/Silverlight/JavaApplet applications, 
 * especially direct P2P-communication purposes.
 * 
 * The endpoints can be any two IPs - but the contact HAS to be initiated by
 * both endpoints at approximately the same time (given TTL for an UDP packet
 * state in the firewall). You can use the server as endpoint #2; this causes
 * an UDP packet to be sent from the server to the client and vice versa, 
 * opening a transparent UDP connection to your client on the port you 
 * specify.
 * 
 * Keep in mind that the port must be clear and probably should be randomized 
 * and kept above 1024.
 * 
 * I take absolutely no responsibility for how you use this. All methods 
 * used are immediately available and do not break any laws at all. Traffic 
 * on the port, however, is of course subject to all relevant laws.
 * 
 * This also works when called as static, i.e. 
 * Tx_Fed_Security_NATHolePuncher::punchHole(12345, $c1, $c2);
 * 
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Security
 */

class Tx_Fed_Security_NATHolePuncher implements t3lib_Singleton {
	
	
	/**
	 * Initiate hole punching between $client1 and $client2 on $port.
	 * After initialization you should be able to talk to both clients on the 
	 * UDP port specified - and both clients should be able to talk to each 
	 * other the same way. 
	 * 
	 * @param int $port
	 * @param string $client1
	 * @param string $client2
	 * @return boolean
	 */
	public function punchHole($port, $client1, $client2) {
		
	}
	
}

?>