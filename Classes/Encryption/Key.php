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
 * Key class. OOP implementation of selective openssl_* operations.
 * 
 * At this time only a few types of keys are supported (see manual). If your 
 * project requires a certain unsuppored key type you may subclass this Key class
 * and use it as you normally would in the Keychain and Crypt services. But, please
 * inform me of about the type of key. Of course you are also welcome to contribute 
 * to this project - contact me for details.
 * 
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Encryption\Key
 */
class Tx_Fed_Encryption_Key {
	
	/**
	 * Location of private key file. DO NOT SPECIFIY THE CONTENT OF THE PRIVATE 
	 * KEY HERE! It will expose the key to dumps. Always specify an absolute path.
	 * 
	 * @var string
	 */
	protected $privateKey;
	
	/**
	 * @var string $fingerprint
	 */
	protected $fingerprint;
	
	/**
	 * @var string $signature
	 */
	protected $signature;
	
	/**
	 * @var string $algorithm
	 */
	protected $algorithm;
	
	/**
	 * Bit-strength of key
	 * @var int $strength
	 */
	protected $strength;
	
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
	 * Set the public key - if a private key is already specified, throw 
	 * Exception with message about an illegal operation. Public key must 
	 * always be derived from private key if it is specified.
	 * Supports chaining.
	 * 
	 * @param string $publicKey
	 * @return Tx_Fed_Encryption_Key
	 * @throws Exception
	 */
	public function setPublicKey($publicKey) {
		if ($this->privateKey && $this->publicKey !== $publicKey) {
			throw new Exeption('Private key defined and $publicKey does not match, denied. Use new Key instead.');
		}
		if ($this->publicKey && $this->publicKey !== $publicKey) {
			throw new Exception('Already have a public key, overwriting is not allowed. Create new Key instead.');
		}
		return $this;
	}
	
	/**
	 * Set the private key. You may override an already set private key - but this 
	 * forces a rehash which derives the public key, fingerprint and signatures. 
	 * 
	 * @param string $privateKey
	 * @return Tx_Fed_Encryption_Key
	 */
	public function setPrivateKey($privateKey) {
		if ($this->privateKey != $privateKey) {
			$this->hash();
		}
		$this->privateKey = $privateKey;
		return $this;
	}
	
	/**
	 * Create derivatives, fingerprint and signatures from configured key(s)
	 * 
	 * @return void
	 */
	protected function hash() {
		
	}
	
	/**
	 * @return string
	 */
	public function getFingerprint() {
		return $this->fingerprint;
	}
	
	/**
	 * @return string
	 */
	public function getSignature() {
		return $this->signature;
	}
	
	/**
	 * @return string
	 */
	public function getAlgorithm() {
		return $this->algorithm;
	}
	
	/**
	 * @return int
	 */
	public function getStrength() {
		return $this->strength;
	}
}

?>