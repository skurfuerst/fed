<?php


class Tx_Fed_View_FlexibleContentElementView extends Tx_Fluid_View_StandaloneView {

	public function getFlexibleContentElementDefinitions() {
		$this->render();
		return $this->baseRenderingContext->getTemplateVariableContainer()->get('FEDFCE');
	}

}


?>
