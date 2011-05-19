

dk.wildside.display.field.Input = function(jQueryElement) {
	dk.wildside.display.field.Field.call(this, jQueryElement);
	this.addEventListener(dk.wildside.event.FieldEvent.CHANGE, this.onChange);
	this.setSanitizer( dk.wildside.display.field.Sanitizer[this.config.sanitizer] );
	this.captureJQueryEvents(['click', 'change'], this.fieldContext, this);
};

dk.wildside.display.field.Input.prototype = new dk.wildside.display.field.Field();

dk.wildside.display.field.Input.prototype.getValue = function() {
	return this.fieldContext.val();
};

dk.wildside.display.field.Input.prototype.setValue = function(val) {
	this.fieldContext.val(val);
	this.value = val;
};