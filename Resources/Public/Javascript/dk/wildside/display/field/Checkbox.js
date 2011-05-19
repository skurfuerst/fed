

dk.wildside.display.field.Checkbox = function(jQueryElement) {
	dk.wildside.display.field.Field.call(this, jQueryElement);
	this.addEventListener(dk.wildside.event.FieldEvent.CHANGE, this.onChange);
	this.captureJQueryEvents(['click', 'change']);
};

dk.wildside.display.field.Checkbox.prototype = new dk.wildside.display.field.Field();

dk.wildside.display.field.Checkbox.prototype.getValue = function() {
	return this.fieldContext.is(':checked') ? 1 : 0;
};