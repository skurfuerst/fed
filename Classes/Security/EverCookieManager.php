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
 * 
 * Now for the "I told you so"-prevention part:
 * 
 * There's a fairly high probability your users will NOT appreciate 
 * you using this class. It thoroughly exploits browser behaviour
 * and leaves the visitor without any control over the cookie. Please
 * only use this where you absolute must have a permanent cookie solution.
 * 
 * Don't use it to track your visitors... respect privacy. Don't be a 
 * douche. Always allow your users to remove the EverCookie.
 * 
 * 
 * 
 * 
 * It employs a limited subset of the features used by the "evercookie"
 * proof-of-concept created by Samy Kamkar, Poland, to create an almost
 * indestructible and permanent cookie on the users' computer.
 * 
 * There are some major issues connected to using this. You were warned.
 * 
 * 
 * 
 * CAVEATS AND WARNINGS
 * 
 * 1) Keep in mind that for the cookies to actually "become" EverCookies, 
 * at least one succesful FE rendering is required AFTER setting the 
 * cookie.
 * 
 * 2) Also keep in mind that the only sure way to delete an EverCookie 
 * is through the "evercookie" library or this EverCookieManager class.
 * 
 * 3) DO NOT register sensitive data! The initial cookie value is transported
 * as clear-text. Hence, NEVER EVER (I -really- mean this) USE THIS ON
 * ANY PAGE WHICH GETS CACHED!
 * 
 * 4) Cookies permeate between browsers supporting the Shared Local Object
 * procedures -unless- LSO data is removed (well, at least until the 
 * user visits a page that re-permeates the EverCookie all over).
 * 
 * 5) It is NOT possible to specify a TTL for this cookie! If MUST be removed
 * manually!
 * 
 * 6) The SecuritySession class uses this method if setEverCookie(TRUE) is used.
 * As a precaution against immediate abuse, the SecuritySession informs the 
 * user's browser that it is in fact being given a cookie which is not likely
 * to vanish any time soon. Additionally, it adds page header data (meta, not
 * visible) informing the visitor of the EverCookie usage.
 * 
 * @author Claus Due, Wildside A/S
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Security
 */

class Tx_Fed_Security_EverCookieManager implements t3lib_Singleton {
	
	// TODO: read above comment for functionality - ready, set, code!
	
}

?>