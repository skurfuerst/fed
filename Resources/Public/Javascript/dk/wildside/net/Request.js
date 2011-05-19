dk.wildside.net.Request = function(widget, action) {
	this.scope = false;
	this.ajax = false;
	this.controller = false;
	this.data = false;
	this.action = action;
	this.widget = widget;
	this.base = false;
	if (typeof widget == 'string') {
		this.url = widget;
	} else {
		this.url = false;
	}
	return this;
};

dk.wildside.net.Request.prototype.getScope = function() {
	if (this.scope) {
		return this.scope;
	};
	return this.widget.config.plugin;
};

dk.wildside.net.Request.prototype.setScope = function(scope) {
	this.scope = scope;
	return this;
};

dk.wildside.net.Request.prototype.getController = function() {
	if (this.controller) {
		return this.controller;
	} else if (this.widget.config.controller) {
		return this.widget.config.controller;
	} else {
		//console.info('Invalid controller configuration');
		//console.warn(this);
	};
};

dk.wildside.net.Request.prototype.setController = function(controller) {
	this.controller = controller;
	return this;
};

dk.wildside.net.Request.prototype.getAction = function() {
	if (this.action) {
		return this.action;
	} else {
		return this.widget.config.action;
	};
};

dk.wildside.net.Request.prototype.setAction = function(action) {
	this.action = action;
	return this;
};

dk.wildside.net.Request.prototype.setData = function(data) {
	this.data = data;
	return this;
};

dk.wildside.net.Request.prototype.getData = function(data) {
	if (this.data) {
		return this.data;
	} else {
		return this.widget.getValues();
	};
};

dk.wildside.net.Request.prototype.setBase = function(base) {
	this.base = base;
	return this;
};

dk.wildside.net.Request.prototype.getBase = function() {
	if (this.base) {
		return this.base;
	} else {
		return this.widget.config.api;
	};
};



dk.wildside.net.Request.prototype.setUrl = function(url) {
	this.url = url;
	return this;
};

dk.wildside.net.Request.prototype.getUrl = function() {
	if (this.url) {
		return this.url;
	};
	//var configuration = this.widget.config;
	var base = this.getBase();
	var scope = this.getScope();
	var action = this.getAction();
	var controller = this.getController();
	var urlParameters = new Array();
	urlParameters.push(scope + '[controller]=' + controller);
	urlParameters.push(scope + '[action]=' + action);
	return base + '&' + urlParameters.join('&');
};

dk.wildside.net.Request.prototype.setWidget = function(widget) {
	this.widget = widget;
	return this;
};

dk.wildside.net.Request.prototype.getWidget = function(widget) {
	return this.widget;
};

dk.wildside.net.Request.prototype.setAjax = function(ajaxObject) {
	this.ajax = ajaxObject;
	return this;
};

dk.wildside.net.Request.prototype.getAjax = function() {
	return this.ajax;
};
