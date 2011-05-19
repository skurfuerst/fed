dk.wildside.net.Dispatcher = function(request) {
	if (request instanceof dk.wildside.net.Request == false) {
		this.request = new dk.wildside.net.Request();
	} else{
		this.request = request;
	}
	this.response = new dk.wildside.net.Response();
	this.responder = new dk.wildside.net.Responder();
	return this;
};

dk.wildside.net.Dispatcher.prototype.execute = function() {
	var responder = this.dispatchRequest();
	return responder.execute();
};

dk.wildside.net.Dispatcher.prototype.dispatchRequest = function(request, parameterWrap) {
	if (request instanceof dk.wildside.net.Request == false) {
		request = this.request;
	};
	if (typeof parameterWrap == 'undefined') {
		parameterWrap = true;
	};
	var data = {};
	var widget = request.getWidget();
	var parent = widget.getParent();
	var scope = request.getScope();
	var configuration = widget.config;
	var controller = request.getController().toLowerCase();
	var objectData = request.getData();
	if (configuration.data.uid > 0) {
		// Patch data; re-address UID to confirm with Extbase controller argument loading
		objectData.__identity = configuration.data.uid;
		delete(objectData.uid);
	};
	data[scope] = {};
	if (parent && typeof parent.config.data != 'undefined' && parent.config.data.uid > 0) {
		data[scope].parent = {
			__identity : parent.config.data.uid
		};
	};
	if (parameterWrap) {
		data[scope][controller] = objectData;
	} else {
		data[scope] = objectData;
	};
	var ajaxOptions = {
		async: false,
		type: 'post',
		url: request.getUrl(),
		data: data
	};
	var ajax = jQuery.ajax(ajaxOptions); // all HTTP happens right away
	request.setAjax(ajax);
	var response = new dk.wildside.net.Response(request);
	var responder = new dk.wildside.net.Responder(response);
	this.setResponse(response);
	this.setResponder(responder);
	return responder;
};

dk.wildside.net.Dispatcher.prototype.setRequest = function(request) {
	this.request = request;
	return this;
};

dk.wildside.net.Dispatcher.prototype.getRequest = function() {
	return this.request;
};

dk.wildside.net.Dispatcher.prototype.setResponse = function(response) {
	this.response = response;
	return this;
};

dk.wildside.net.Dispatcher.prototype.getResponse = function() {
	return this.response;
};

dk.wildside.net.Dispatcher.prototype.setResponder = function(responder) {
	this.responder = responder;
	return this;
};

dk.wildside.net.Dispatcher.prototype.getResponder = function() {
	return this.responder;
};

