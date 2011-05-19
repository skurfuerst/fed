

dk.wildside.display.field.Radio = function() {
	dk.wildside.display.field.Field.apply(this, arguments);
	this.addEventListener(dk.wildside.event.FieldEvent.CHANGE, this.onChange);
	this.captureJQueryEvents(['click', 'change'], this.fieldContext, this);
};

dk.wildside.display.field.Radio.prototype = new dk.wildside.display.field.Field();

dk.wildside.display.field.Radio.prototype.getValue = function() {
	var value = this.fieldContext.parents("." + this.selectors.field + ":first").find(":checked").attr("value");
	return value;
};