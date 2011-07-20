<?php


class Tx_Fed_View_ExposedTemplateView extends Tx_Fluid_View_StandaloneView {

	public function harvest($name) {
		$this->templateSource = file_get_contents($this->templatePathAndFilename);
		$this->baseRenderingContext->setControllerContext($this->controllerContext);
		$this->templateParser->setConfiguration($this->buildParserConfiguration());
		$parsedTemplate = $this->templateParser->parse($this->templateSource);
		$this->startRendering(Tx_Fluid_View_AbstractTemplateView::RENDERING_TEMPLATE, $parsedTemplate, $this->baseRenderingContext);
		$value = $parsedTemplate->getVariableContainer()->get($name);
		if ($value instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode) {
			$childNodes = $value->getChildNodes();
			$value = array('fields' => array(), 'grid' => array(), 'preview' => NULL);
			foreach ($childNodes as $node) {
				if ($node instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode === FALSE) {
					continue;
				}
				$className = $node->getViewHelperClassName();
				$dummy = $this->objectManager->get($className);
				$config = $node->evaluate($this->baseRenderingContext);
				$name = $config['name'];
				if ($dummy instanceof Tx_Fed_ViewHelpers_Fce_FieldViewHelper) {
					$value['fields'][$name] = $config;
				} else if ($className == 'Tx_Fed_ViewHelpers_Fce_PreviewViewHelper') {
					$value['preview'] = $config;
				} else if ($className == 'Tx_Fed_ViewHelpers_Fce_GridViewHelper') {
					$grid = array();
					foreach ($node->getChildNodes() as $rowNode) {
						if ($rowNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode === FALSE) {
							continue;
						}
						$row = array();
						$rowNode->evaluateChildNodes($this->baseRenderingContext);
						foreach ($rowNode->getChildNodes() as $columnNode) {
							if ($columnNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode === FALSE) {
								continue;
							}
							$column = $columnNode->evaluate($this->baseRenderingContext);
							$areas = array();
							foreach ($columnNode->getChildNodes() as $areaNode) {
								if ($areaNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode === FALSE) {
									continue;
								}
								$area = $areaNode->evaluate($this->baseRenderingContext);
								$areas[$area['name']] = $area;
							}
							$column['areas'] = $areas;
							$row[] = $column;
						}
						$grid[] = $row;
					}
					$value['grid'] = $grid;
				}
			}
		}
		$this->stopRendering();
		return $value;
	}

}


?>
