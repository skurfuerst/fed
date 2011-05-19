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
 * @subpackage ViewHelpers\Extbase\Widget
 */
class Tx_Fed_ViewHelpers_Extbase_Widget_PdfViewHelper extends Tx_Fed_ViewHelpers_Extbase_WidgetViewHelper {
	
	/**
	 * @var Tx_Fed_Utility_PDF
	 */
	protected $pdf;
	
	/**
	 * @author Claus Due, Wildside A/S
	 * @param Tx_Fed_Utility_PDF $pdf
	 */
	public function injectPDF(Tx_Fed_Utility_PDF $pdf) {
		$this->pdf = $pdf;
	}
	
	/**
	 * Initialize arguments for this ViewHelper
	 * 
	 * @author Claus Due, Wildside A/S
	 * @return void
	 */
	public function initializeArguments() {
		$arguments = $this->pdfService->getViewHelperArguments();
		foreach ($arguments as $name=>$config) {
			list ($type, $description, $required, $default) = $config;
			$this->registerArgument($name, $type, $description, $required, $default);
		}
		$this->registerArgument('filename', 'string', 'Download as filename', TRUE, 'file.pdf');
		$this->registerArgument('wkhtmltopdf', 'string', 'Path to executable wkhtmltopdf binary', FALSE, 'wkhtmltopdf');
	}
	
	/**
	 * Get an array of the arguments used to implement this instance of the 
	 * ViewHelper.
	 * 
	 * @author Claus Due, Wildside A/S
	 * @return array
	 */
	protected function getDefinedArguments() {
		$arguments = $this->pdfService->getViewHelperArguments();
		$defined = array();
		foreach ($arguments as $name=>$config) {
			if ($this->hasArgument($name)) {
				$defined[$name] = $config;
			}
		}
		return $defined;
	}
	
	/**
	 *  
	 * @author Claus Due, Wildside A/S
	 * @return string
	 */
	public function render() {
		$uniqId = uniqid('fedPDF_');
		$extension = $this->arguments['extensionName'];
		$arguments = $this->jsonService->encode($this->getDefinedArguments());
		$arguments = base64_encode($arguments);
		$typeNum = 48151623420;
		$code = <<< CODE
<form action='?type={$typeNum}' method='post' id='{$uniqId}' style='display: none'>
<input type='hidden' name='{$extension}[html]' value='' />
<input type='hidden' name='{$extension}[filename]' value='{$this->arguments['filename']}' />
<input type='hidden' name='{$extension}[wkhtmltopdf]' value='{$this->arguments['wkhtmltopdf']}' />
<input type='hidden' name='{$extension}[arguments]' value='{$arguments}' />
</form>
CODE;
		$script = <<< SCRIPT
function {$uniqId}() {
	var f = jQuery('#{$uniqId}');
	f.find('input[name="{$extension}[html]"]').val('<html><head>'+document.head.innerHTML+'</head><body>'+document.body.innerHTML+'</body></html>');
	return f.submit();
}
SCRIPT;
		
		$this->includeHeader($script, 'js');
		
		$html .= $code;
		$inner = parent::renderChildren();
		$html = "<a href='javascript:;' class='fed-pdf-link' onclick='{$uniqId}();'>{$inner}</a>";
		return $html; 
	}

	
	
}


?>