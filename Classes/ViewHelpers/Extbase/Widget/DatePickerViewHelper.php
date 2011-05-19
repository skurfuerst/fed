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
class Tx_Fed_ViewHelpers_Extbase_Widget_DatePickerViewHelper extends Tx_Fed_ViewHelpers_Extbase_WidgetViewHelper {
	
	const NS = 'dk.wildside.display.widget.DatePickerWidget';
	
	/**
	 * Creates a jQuery datepicker element
	 * 
	 * @param string $widget JS namespace of widget to use - override this if you subclassed dk.wildside.display.widget.DatePickerWidget in JS
	 * @param string $name Name of the emulated field
	 * @param string $controller Controller name
	 * @param string $plugin Plugin name to use
	 * @param string $action Action of controller
	 * @param string $date The selected date to use, either UNIX timestamp or strtotime-compat date string
	 * @param array $dates Array of dates to highlight
	 * @param string $class The class of the input field
	 * @param string $title The title of the Widget
	 * @param string $templateFile siteroot-relative path of template file to use
	 * @param int $type
	 * @param string $dateFormat Internal format of the timestamp, used for output
	 * @param string $dateFormatJS Format of the timestamp, as sent to the jQueryUI widget
	 * @return string
	 */
	public function render(
			$widget=self::NS, 
			$name='date',
			$controller=NULL,
			$plugin=NULL,
			$action=NULL,
			$date=NULL,
			$dates=array(),
			$class=NULL,
			$title=NULL,
			$templateFile=NULL,
			$type=0,
			$dateFormat="d/m/Y",
			$dateFormatJS="dd/mm/yy"
			) {
		$page = NULL;
		$data = new stdClass();
		$data->value = $date;
		$data->name = $name;
		$data->dateFormat = $dateFormatJS;
		$html = $this->renderChildren();
		if (strlen(trim($html)) == 0) {
			$defaultTemplateFile = 'Widget/DatePickerWidget.html';
			$template = $this->getTemplate($templateFile, $defaultTemplateFile);
			$template->assign('name', $name);
			$template->assign('date', $date);
			
			if ($date) {
				$template->assign('dateDisplayed', date($dateFormat, $date));
			}
			
			$template->assign('dates', $dates);
			$html = $template->render();
		}
		return parent::render($widget, $name, $controller, $action, $page, $plugin, $data, $class, $title, $type, $html);
	}
}
	

?>