<?php


class Tx_Fed_View_FlexibleContentElementView extends Tx_Fluid_View_StandaloneView {

	protected $flexFormData;

	/**
	 *
	 * @param array $flexFormData
	 */
	public function setFlexFormData($data) {
		$this->flexFormData = $data;
	}

	public function getFlexibleContentElementDefinitions() {
		if ($this->flexFormData) {
			$this->assignMultiple($this->flexFormData);
		}
		$this->render();
		return $this->baseRenderingContext->getTemplateVariableContainer()->get('FEDFCE');
	}

}


?>
