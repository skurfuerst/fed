dk.wildside.net.Response = function(request) {
	this.request = null;
	this.raw = null;
	this.json = null;
	this.setRequest(request);
	return this;
};

dk.wildside.net.Response.prototype.setRequest = function(request) {
	this.request = request;
	return this;
};

dk.wildside.net.Response.prototype.getRequest = function() {
	return this.request;
};

dk.wildside.net.Response.prototype.getAjax = function() {
	return this.getRequest().getAjax();
};

dk.wildside.net.Response.prototype.getData = function() {
	var ajax = this.getAjax();
	if (ajax.status == 200) {
		var json = jQuery.parseJSON(ajax.responseText);
		return json;
	} else {
		//this.trace('invalid return code (not 200): ' + ajax.status, 'warn');
		return {};
	};
};