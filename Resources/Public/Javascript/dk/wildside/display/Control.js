/***************************************************************
* Widget Control - Gui Object
* 
* Standard HTML user interface control with interaction.
* 
***************************************************************/


dk.wildside.display.Control = function(jQuerySelector) {
	
	
	dk.wildside.display.DisplayObject.apply(this, arguments);
};

dk.wildside.display.Control.prototype = new dk.wildside.display.DisplayObject();