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
class Tx_Fed_ViewHelpers_Extbase_Widget_FileUploadViewHelper extends Tx_Fed_ViewHelpers_Extbase_WidgetViewHelper {
	
	const NS = 'dk.wildside.display.widget.FileUploadWidget';
	
	/**
	 * Render an entry for a Listener compatible with JS LocusController
	 * @param string $widget JS namespace of widget to use - override this if you subclassed dk.wildside.display.widget.FileUploadWidget in JS
	 * @param string $name Name of the emulated field
	 * @param array $data Prefilled files
	 * @param string $class Extra CSS-classes to use
	 * @param string $title Title of the widget
	 * @param int $type TypeNum, if any, for building request URI
	 * @param string $template siteroot-relative path of template file to use - leave out for default
	 * @return string
	 */
	public function render($widget=self::NS, $name='files', $data=NULL, $class=NULL, $title=NULL, $templateFile=NULL) {
		$type = 4815163242;
		$controller = 'FileUpload';
		$action = 'upload';
		$plugin = 'tx_wildsideextbase_api';
		$html = $this->renderChildren();
		if (strlen(trim($html)) == 0) {
			$defaultTemplateFile = 'Widget/FileUploadWidget.html';
			$template = $this->getTemplate($templateFile, $defaultTemplateFile);
			$html = $template->render();
		}
		return parent::render($widget, $name, $controller, $action, $page, $plugin, $data, $class, $title, $type, $html);
	}
	
}


?>