

dk.wildside.display.field.Select = function(jQueryElement) {
	dk.wildside.display.field.Field.call(this, jQueryElement);
	this.fieldContext = this.context.find('select');
	this.captureJQueryEvents(['change']);
};

dk.wildside.display.field.Select.prototype = new dk.wildside.display.field.Field();

dk.wildside.display.field.Select.prototype.getValue = function() {
	return this.fieldContext.val();
};