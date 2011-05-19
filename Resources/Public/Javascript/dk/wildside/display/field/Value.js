

dk.wildside.display.field.Value = function(jQueryElement) {
	
	if (jQueryElement) {
		dk.wildside.display.field.Field.call(this, jQueryElement);
	};
};

dk.wildside.display.field.Value.prototype = new dk.wildside.display.field.Field();

dk.wildside.display.field.Value.prototype.getValue = function() {
	return this.context.html();
};