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
 * Crypt service - encrypt/decrypt using selective encryption algorithms 
 * and keys.1
 * 
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Encryption\Crypt
 */
class Tx_Fed_Encryption_Crypt implements t3lib_Singleton {
	
	/**
	 * @var Tx_Fed_Encryption_Keyxhain $keychain
	 */
	protected $keychain;
	
	/**
	 * @param Tx_Fed_Encryption_Keychain $keychain
	 */
	public function injectKeychain(Tx_Fed_Encryption_Keychain $keychain) {
		$this->keychain = $keychain;
	}
	
	/**
	 * Encrypt $content using a custom key definition. If $content is not a 
	 * string, it will be converted. If it is an object which cannot be cast 
	 * as a string an attempt serialize will occur. If the value looks invalid, 
	 * i.e. does not reconstitute, an Exception is thrown. When decrypting the 
	 * result is inspected for a likely serialized value, in which case we unserialize
	 * - but remember that unserialization is the default serialization routines
	 * so the returned value will be an instance of "stdClass" with public 
	 * properties. Keep this in mind always when working with DomainObjects.
	 * 
	 * @param mixed $content
	 * @param Tx_Fed_Encryption_Key $key
	 * @return string
	 * @throws Exception
	 */
	public function encrypt($content, Tx_Fed_Encryption_Key $key) {
		$encrypted = $content;
		return $encrypted;
	}
	
	/**
	 * Attempt decryption of $content. If the decryption is succesful and the 
	 * content looks like a serialized value then perform unserialization 
	 * before return. Hence mixed return type.
	 * 
	 * @param string $content
	 * @param Tx_Fed_Encryption_Key $key
	 * @return mixed
	 */
	public function decrypt($content, Tx_Fed_Encryption_Key $key) {
		$decrypted = $content;
		return $decrypted;
	}
	
	/**
	 * Signs encrypted $content.
	 * 
	 * @param mixed $content
	 * @param Tx_Fed_Encryption_Key $key
	 */
	public function sign($content, Tx_Fed_Encryption_Key $key) {
		$signed = $content;
		return $signed;
	}
	
	/**
	 * Signs and seals encrypted $content for transmission
	 * 
	 * @param string $content
	 * @param Tx_fed_Entryption_Key $key
	 */
	public function seal($content, Tx_fed_Entryption_Key $key) {
		$signed = $content;
		$sealed = $signed;
		return $sealed;
	}
	
}


?>