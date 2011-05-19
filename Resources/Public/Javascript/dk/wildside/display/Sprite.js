dk.wildside.display.Sprite = function(jQueryElement) {
	if (typeof jQueryElement == 'undefined') {
		return this;
	};
	this.identity = 'sprite';
	dk.wildside.display.DisplayObject.call(this, jQueryElement);
	if (this.config) {
		this.setName(this.config.name);
		this.setValue(this.config.value);
	};
	var events = ['click'];
	//var realContext = this.context.children().last();
	var realContext = this.context.find(">*:last");
	if (realContext) {
		realContext.data('instance', this);
		this.captureJQueryEvents.call(this, events, realContext);
	} else {
		this.captureJQueryEvents.call(this, events);
	};
	//this.addEventListener(dk.wildside.event.MouseEvent.CLICK, this.onClick);
};

dk.wildside.display.Sprite.prototype = new dk.wildside.display.DisplayObject();

dk.wildside.display.Sprite.prototype.html = function(str) {
	return this.context.html(str);
};

dk.wildside.display.Sprite.prototype.getName = function() {
	return this.config.name;
};

dk.wildside.display.Sprite.prototype.setName = function(name) {
	this.config.name = name;
};

dk.wildside.display.Sprite.prototype.getValue = function() {
	return this.config.value;
};

dk.wildside.display.Sprite.prototype.setValue = function(value) {
	this.config.value = value;
};