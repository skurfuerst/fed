

dk.wildside.display.field.Aloha = function(jQueryElement) {
	if (typeof jQueryElement == 'undefined') {
		return this;
	};
	dk.wildside.display.field.Field.call(this, jQueryElement);
	this.fieldContext = this.context.find('.aloha').aloha();
	this.fieldContext.data('field', this);
	this.setSanitizer(dk.wildside.display.field.Sanitizer.trim);
	this.lastValue = this.getValue();
	this.dirty = false;
};

dk.wildside.display.field.Aloha.prototype = new dk.wildside.display.field.Field();


dk.wildside.display.field.Aloha.prototype.getName = function() {
	return this.config.name;
};

dk.wildside.display.field.Aloha.prototype.getValue = function() {
	var id = this.fieldContext.attr('id');
	value = GENTICS.Aloha.getEditableById(id).getContents();
	if (this.fieldContext.hasClass(this.selectors.alohaConfigBasic)) {
		// If this field matches the basic configuration, use the prototype'd strip_tags() to
		// remove all HTML from the value. Hopefully there should be nothing in there, but
		// in rare cases, Aloha can mess up and send some along anyway. This would break the 
		// JSON return, and we can't have that.
		value = value.strip_tags();
	};
	return value;
};

dk.wildside.display.field.Aloha.prototype.endEdit = function() {
	this.dirty = true; // mark dirty; next timer call will dispatch dirty and clear timer
};

dk.wildside.display.field.Aloha.prototype.beginEdit = function() {
	this.onTimer(); // initialize the dirty-check timer
};

dk.wildside.display.field.Aloha.prototype.onTimer = function() {
	if (this.dirty == true) {
		this.dispatchEvent(dk.wildside.event.FieldEvent.DIRTY);
		this.lastValue = value;
		this.dirty = false;
		clearInterval(this.timer);
	} else {
		var issuer = this;
		this.timer = setTimeout(function() {
			issuer.onTimer.call(issuer);
		}, 1000);
	};
};