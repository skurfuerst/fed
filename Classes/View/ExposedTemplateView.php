<?php


class Tx_Fed_View_ExposedTemplateView extends Tx_Fluid_View_StandaloneView {

	public function harvest($name) {
		$this->templateSource = file_get_contents($this->templatePathAndFilename);
		$this->baseRenderingContext->setControllerContext($this->controllerContext);
		$this->templateParser->setConfiguration($this->buildParserConfiguration());
		$parsedTemplate = $this->templateParser->parse($this->templateSource);
		$this->startRendering(Tx_Fluid_View_AbstractTemplateView::RENDERING_TEMPLATE, $parsedTemplate, $this->baseRenderingContext);
		if ($parsedTemplate->getVariableContainer()->exists($name) === FALSE) {
			return NULL;
		}
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
					if ($config['repeat'] > 1) {
						$label = $config['label'];
						for ($f=0; $f<$config['repeat']; $f++) {
							$num = $f+1;
							$config['name'] = $name . $num;
							$config['label'] = $label . ' #' . $num;
							$value['fields'][$name . $num] = $config;
						}
					} else {
						$value['fields'][$name] = $config;
					}
				} else if ($className == 'Tx_Fed_ViewHelpers_Fce_PreviewViewHelper') {
					$value['preview'] = $config;
				} else if ($className == 'Tx_Fed_ViewHelpers_Fce_GridViewHelper') {
					$grid = $node->evaluate($this->baseRenderingContext);
					foreach ($node->getChildNodes() as $rowNode) {
						if ($rowNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode === FALSE) {
							continue;
						}
						$row = $rowNode->evaluate($this->baseRenderingContext);
						for ($r=0; $r<$row['repeat']; $r++) {
							$row = $rowNode->evaluate($this->baseRenderingContext);
							foreach ($rowNode->getChildNodes() as $columnNode) {
								if ($columnNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode === FALSE) {
									continue;
								}
								$column = $columnNode->evaluate($this->baseRenderingContext);
								for ($i=0; $i<$column['repeat']; $i++) {
									$areas = array();
									foreach ($columnNode->getChildNodes() as $areaNode) {
										if ($areaNode instanceof Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode === FALSE) {
											continue;
										}
										$area = $areaNode->evaluate($this->baseRenderingContext);
										if ($column['repeat'] > 1 || $row['repeat'] > 1) {
											$area['name'] .= ($r+1).($i+1);
										}
										$area['label'] .= ($row['repeat'] > 1 ? ' #' . ($r+1) : '') . ($column['repeat'] > 1 ? ' #' . ($i+1) : '');
										$areas[$area['name']] = $area;
									}
									$column['areas'] = $areas;
									$row['columns'][] = $column;
								}
							}
							$grid['rows'][] = $row;
						}
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
