<?php


class Tx_Fed_View_FlexibleContentElementView extends Tx_Fed_View_ExposedTemplateView {

	public function getFlexibleContentElementDefinitions() {
		return $this->harvest('FEDFCE');
	}

}


?>
