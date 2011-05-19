

dk.wildside.event.EventDispatcher = function(jQueryElement) {
	if (typeof jQueryElement == 'undefined') {
		return this;
	};
	this.listeners = {};
	this.parent = false;
	this.id = false;
};

dk.wildside.event.EventDispatcher.prototype.getId = function() {
	if (this.id == false) {
		this.id = dk.wildside.objectManager.createId();
		dk.wildside.objectManager.register(this);
	};
	return this.id;
};

dk.wildside.event.EventDispatcher.prototype.setId = function(id) {
	this.id = id;
};

dk.wildside.event.EventDispatcher.prototype.setParent = function(parent) {
	if (typeof parent == 'object') {
		this.parent = dk.wildside.objectManager.register(parent);
	} else {
		this.parent = false;
	};
};

dk.wildside.event.EventDispatcher.prototype.getParent = function() {
	if (this.parent > 0) {
		return dk.wildside.objectManager.fetch(this.parent);
	} else {
		return false;
	};
};

dk.wildside.event.EventDispatcher.prototype.initializeIfMissing = function(eventType) {
	try {
		if (typeof this.listeners[eventType] == 'undefined') {
			this.listeners[eventType] = new dk.wildside.util.Iterator();
		};
	} catch (e) {
		//console.log(eventType);
		//console.log(this);
	};
};

dk.wildside.event.EventDispatcher.prototype.hasEventListener = function(eventType, func) {
	if (typeof scope == 'undefined') {
		scope = this;
	};
	this.initializeIfMissing(eventType);
	return this.listeners[eventType].contains(func);
};

dk.wildside.event.EventDispatcher.prototype.addEventListener = function(eventType, func) {
	if (typeof eventType == 'undefined') {
		this.trace('Invalid event type: ' + eventType);
	};
	if (typeof scope == 'undefined') {
		scope = this;
	};
	this.initializeIfMissing(eventType);
	this.listeners[eventType].push(func);
	return this;
};

dk.wildside.event.EventDispatcher.prototype.removeEventListener = function(eventType, func) {
	if (typeof scope == 'undefined') {
		scope = this;
	};
	if (typeof func == 'undefined') {
		this.listeners[eventType] = new dk.wildside.util.Iterator();
	} else {
		this.initializeIfMissing(eventType);
		this.listeners[eventType] = this.listeners[eventType].remove(func);
	};
	return this;
};

dk.wildside.event.EventDispatcher.prototype.dispatchEvent = function(event) {
	var instance = this;
	var newID = Math.round(Math.random()*100000);
	if (typeof event == 'string') {
		event = new dk.wildside.Event(event, this);
	} else if (event.handleObj) {
		// is a jQuery event - transform to native event and attach originalEvent
		instance = dk.wildside.objectManager.fetch( jQuery(instance).data('instance') );
		event = new dk.wildside.Event(event.type, instance);
		return instance.dispatchEvent.call(instance, event);
	};
	//console.info('Dispatching event: '+event.type+' with ID ' + event.id + '. My identity: ' + instance.identity);
	
	event.setCurrentTarget(instance);
	if (typeof instance.listeners[event.type] != 'undefined') {
		instance.listeners[event.type].each(function(func) {
			func.call(event.getCurrentTarget(), event);
		});
	};
	var parent = instance.getParent();
	if (parent && event.cancelled == false) {
		parent.dispatchEvent.call(parent, event);
	} else {
		delete(event); // reached top level, remove all traces of event
	};
	return instance;
};

dk.wildside.event.EventDispatcher.prototype.captureJQueryEvents = function(onlyEvents, context, parent) {
	if (typeof context == 'undefined') {
		context = this.context;
	};
	if (typeof parent == 'undefined') {
		parent = this;
	};
	var events = new dk.wildside.util.Iterator();
	if (typeof onlyEvents == 'array') {
		events.merge(onlyEvents);
	} else {
		events.merge(['change', 'click', 'keydown', 'keyup', 'keypress', 'focus', 'blur',
		              'mouseover', 'mousemove', 'mouseenter', 'mouseleave', 'mouseup', 
		              'mousedown', 'resize', 'select', 'scroll', 'submit']);
	};
	events.each(function(eventType) {
		context.bind(eventType, parent.dispatchEvent).data('instance', parent);
	});

};
