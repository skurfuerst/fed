/***************************************************************
* Widget - Base Class
* 
* Base class for interactive interface objects (widgets) used 
* to manipulate data, collect data from the object, update 
* the object's visual elements using data returned from controller 
* calls and various GUI-related operations relevant for interactive 
* elements (such as enable and disable).
* 
***************************************************************/

dk.wildside.display.widget.Widget = function(jQueryElement) {
	if (typeof jQueryElement == 'undefined') {
		return this;
	};
	dk.wildside.display.DisplayObject.call(this, jQueryElement);
	var widget = this; // Necessary backreference for closures
	this.events = dk.wildside.event.widget.WidgetEvent;
	this.selectors = dk.wildside.util.Configuration.guiSelectors;
	this.messages = new dk.wildside.util.Iterator();
	this.errors = new dk.wildside.util.Iterator();
	this.disabled = false;
	this.dirty = false;
	this.name = this.config.name;
	this.value = this.config.value;
	this.defaultAction = this.config.action;
	this.identity = 'widget';
	this.addEventListener(this.events.DIRTY, this.onDirty);
	this.addEventListener(this.events.CLEAN, this.onClean);
	this.addEventListener(this.events.ERROR, this.onError);
	this.addEventListener(this.events.REFRESH, this.onRefresh);
	this.addEventListener(this.events.MESSAGE, this.onMessage);
	this.addEventListener(dk.wildside.event.FieldEvent.DIRTY, this.onDirtyField);
	this.addEventListener(dk.wildside.event.FieldEvent.CLEAN, this.onCleanField);
	this.context.find("." + this.selectors.widget +":not(." + this.selectors.inUse +")")
	.not(this.context.find("." + this.selectors.widget +" ." + this.selectors.widget))
	.each( function() {
		var obj = jQuery(this);
		var widgetAsField = dk.wildside.spawner.get(obj);
		widget.addChild.call(widget, widgetAsField); // will catch events from sub-Widgets
	} );
	this.context.find("." + this.selectors.field)
	.not(this.context.find("." + this.selectors.widget + " ." + this.selectors.field))
	.each(function() {
		var obj = jQuery(this);
		var field = dk.wildside.spawner.get(obj);
		widget.addChild.call(widget, field);
	});
	return this;
};

dk.wildside.display.widget.Widget.prototype = new dk.wildside.display.DisplayObject();



// DISPLAY MANIPULATION METHODS
dk.wildside.display.widget.Widget.prototype.enableControls = function() {
	this.dispatchEvent(this.events.PRE_ENABLE);
	this.disabled = false;
	// TODO: enable controls in GUI
	this.dispatchEvent(this.events.ENABLED);
};

dk.wildside.display.widget.Widget.prototype.disableControls = function() {
	this.dispatchEvent(this.events.PRE_DISABLE);
	this.disabled = true;
	// TODO: disable controls in GUI
	this.dispatchEvent(this.events.DISABLED);
};

dk.wildside.display.widget.Widget.prototype.displayErrors = function(errors) {
	var container = this.children.find('errors');
	if (typeof container != 'undefined') {
		return container.setValue.call(container, errors);
	} else {
		var error = new String();
		for (var i=0; i<errors.length; i++) {
			var msg = errors[i];
			if (typeof(msg) == "string") {
				error += msg;
			} else if (typeof(msg) == "object"){
				error += msg.title + ' ' + msg.message + ' (' + msg.severity + ')';
			};
		}
		return alert(error);
	};
};

dk.wildside.display.widget.Widget.prototype.displayMessages = function(messages) {
	var container = this.children.find('messages');
	if (typeof container != 'undefined') {
		return container.setValue.call(container, messages);
	} else {
		var message = new String();
		for (var i=0; i<messages.length; i++) {
			var msg = messages[i];
			message += msg.title + ' ' + msg.message + ' (' + msg.severity + ')';
		}
		return alert(message);
	};
};





// DATA MANIPULATION METHODS
dk.wildside.display.widget.Widget.prototype.setValue = function(value) {
	// default action is to do nothing - custom Widgets masquerading as Fields override this method
	this.value = value;
};

dk.wildside.display.widget.Widget.prototype.getValue = function() {
	// default action is to return undefined - custom Widgets masquerading as Fields override this method
	return this.value;
};

dk.wildside.display.widget.Widget.prototype.setName = function(newName) {
	this.name = newName;
};

dk.wildside.display.widget.Widget.prototype.getName = function() {
	return this.name;
};

dk.wildside.display.widget.Widget.prototype.setValues = function(object) {
	for (var name in object) {
		var child = this.children.find(name);
		if (child) {
			child.setValue(object[name]);
		};
		this.config.data[name] = object[name];
	};
	this.markClean();
	this.dispatchEvent(this.events.UPDATED);
};

dk.wildside.display.widget.Widget.prototype.getValues = function() {
	var values = {};
	var widget = this;
	this.children.each(function(field) {
		var fieldName = field.getName();
		if (typeof widget.config.data[fieldName] != 'undefined') {
			values[fieldName] = field.getValue();
		};
	});
	return values;
};







// MODEL OBJECT INTERACTION METHODS
dk.wildside.display.widget.Widget.prototype.markDirty = function() {
	this.dirty = true;
	this.dispatchEvent(dk.wildside.event.widget.WidgetEvent.DIRTY);
};

dk.wildside.display.widget.Widget.prototype.markClean = function() {
	this.dispatchEvent(dk.wildside.event.widget.WidgetEvent.CLEAN);
	this.dirty = false;
};

dk.wildside.display.widget.Widget.prototype.rollback = function() {
	this.children.each(function(field) { field.rollback(); });
};

dk.wildside.display.widget.Widget.prototype.remove = function() {
	this.dispatchEvent(this.events.PRE_DELETE);
	this.config.action = 'delete';
	this.sync();
	return this;
};

dk.wildside.display.widget.Widget.prototype.update = function() {
	this.dispatchEvent(this.events.PRE_UPDATE);
	this.config.action = 'update';
	this.sync();
	return this;
};

dk.wildside.display.widget.Widget.prototype.create = function() {
	this.dispatchEvent(this.events.PRE_CREATE);
	this.markDirty(); // tell the widget that sync() should be performed...
	if (this.config.data.uid > 0) {
		this.config.action = 'copy'; // ...with the "copy" action
	} else {
		this.config.action = 'create'; // ...with the "create" action instead
	};
	this.sync(); // to finally create records and reload this.config.data with new info
	this.config.action = this.defaultAction; // and reset the action to "update"
	return this;
};

dk.wildside.display.widget.Widget.prototype.sync = function() {
	if (this.dirty == false) {
		this.dispatchEvent(this.events.CLEAN);
		return this;
	};
	this.dispatchEvent(this.events.PRE_SYNC);
	
	this.context.find(".loadingIndicator").show();
	
	var request = new dk.wildside.net.Request(this);
	var responder = new dk.wildside.net.Dispatcher(request).dispatchRequest(request);
	var data = responder.getData();
	var messages = responder.getMessages();
	var errors = responder.getErrors();
	
	if (errors.length) {
		this.errors.clear();
		this.errors.merge(errors);
		return this.dispatchEvent(this.events.ERROR);
	} else {
		this.setValues(data);
		this.dispatchEvent(this.events.CLEAN);
		if (messages.length) {
			this.messages.clear();
			this.messages.merge(messages);
			this.dispatchEvent(this.events.MESSAGE);
		};
	};
	
	this.context.find(".loadingIndicator").hide();
	
	return this;
};

dk.wildside.display.widget.Widget.prototype.refresh = function() {
	
};

dk.wildside.display.widget.Widget.prototype.dispatchRequest = function(request, parameterWrap) {
	// TODO: use new request with action=request, controller=Hash and arguments fieldNames:array and fieldNamePrefix:string
	// TODO: insert resulting __hmac into request data
	var responder = new dk.wildside.net.Dispatcher(request).dispatchRequest(request, parameterWrap);
	var data = responder.getData();
	var messages = responder.getMessages();
	var errors = responder.getErrors();
	if (errors.length > 0) {
		return this.dispatchEvent(this.events.ERROR);
	} else {
		if (messages.length > 0) {
			this.dispatchEvent(this.events.MESSAGE);
		};
		return {'data': data, 'messages': messages, 'errors': errors};
	};
};











// EVENT LISTENER METHODS
dk.wildside.display.Component.prototype.onRefresh = function(event) {
	
};

dk.wildside.display.widget.Widget.prototype.onError = function(event) {
	var errors = this.errors;
	this.displayErrors(errors);
};

dk.wildside.display.widget.Widget.prototype.onDirtyField = function(event) {
	this.dispatchEvent(dk.wildside.event.widget.WidgetEvent.DIRTY);
};

dk.wildside.display.widget.Widget.prototype.onCleanField = function(event) {
	//this.dispatchEvent(this.events.DIRTY);
};

dk.wildside.display.widget.Widget.prototype.onDirty = function(event) {
	this.dirty = true;
	if (this.component == false) {
		this.sync();
	};
};

dk.wildside.display.widget.Widget.prototype.onClean = function(event) {
	this.dirty = false;
};

dk.wildside.display.widget.Widget.prototype.onMessage = function(event) {
	var messages = this.messages;
	this.displayMessages(messages);
};

