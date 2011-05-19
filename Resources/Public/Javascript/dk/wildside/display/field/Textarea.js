

dk.wildside.display.field.Textarea = function(jQueryElement) {
	if (typeof jQueryElement == 'undefined') {
		return this;
	};
	dk.wildside.display.field.Field.call(this, jQueryElement);
	this.fieldContext = this.context.find('textarea');
	this.addEventListener(dk.wildside.event.FieldEvent.BLUR, this.onChange);
	this.captureJQueryEvents(['blur'], this.fieldContext, this);
};

dk.wildside.display.field.Textarea.prototype = new dk.wildside.display.field.Field();

dk.wildside.display.field.Textarea.prototype.getValue = function() {
	return this.fieldContext.val();
};