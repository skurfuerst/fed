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
 * RPC Responder.
 * 
 * Uses SSH/2 RPC calls to communicate with other installations of TYPO3
 * running FED. You can configure responders and inject private keys and 
 * authorized public keys by using Tx_Fed_Encryption_Keychain.
 * 
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage RPC
 */
class Tx_Fed_RPC_Responder implements t3lib_Singleton {
	
	/**
	 * @var Tx_Fed_Encryption_Keyxhain $keychain
	 */
	protected $keychain;
	
	/**
	 * @var Tx_Fed_Encryption_Crypt $crypt
	 */
	protected $crypt;
	
	/**
	 * @param Tx_Fed_Encryption_Crypt $crypt
	 */
	public function injectCrypt(Tx_Fed_Encryption_Crypt $crypt) {
		$this->crypt = $crypt;
	}
	
	/**
	 * @param Tx_Fed_Encryption_Keychain $keychain
	 */
	public function injectKeychain(Tx_Fed_Encryption_Keychain $keychain) {
		$this->keychain = $keychain;
	}
	
	
}


?>