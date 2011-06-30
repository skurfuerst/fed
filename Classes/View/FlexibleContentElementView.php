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

	public function harvest($name) {
		if ($this->flexFormData) {
			$this->assignMultiple($this->flexFormData);
		}
		$this->render();
		$templateVariableContainer = $this->baseRenderingContext->getTemplateVariableContainer();
		if ($templateVariableContainer->exists($name)) {
			return $templateVariableContainer->get($name);
		} else {
			return NULL;
		}
	}

	public function getFlexibleContentElementDefinitions() {
		return $this->harvest('FEDFCE');
	}

}


?>
