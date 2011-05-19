dk.wildside.event.Event = {
	DIRTY : 'dirty',
	CLEAN : 'clean',
	SYNC : 'sync',
	ERROR : 'error',
	UNKNOWN : 'unknown'
};

dk.wildside.Event = function(type, target) {
	this.type = false;
	this.target = false;
	this.currentTarget = false;
	this.cancelled = false;
	if (typeof type == 'undefined') {
		type = this.UNKNOWN;
	};
	if (typeof target == 'undefined') {
		target = null;
	};
	this.setType(type);
	this.setTarget(target);
	this.setCurrentTarget(target);
	return this;
};

dk.wildside.Event.prototype.getType = function() {
	return this.type;
};

dk.wildside.Event.prototype.setType = function(type) {
	this.type = type;
};

dk.wildside.Event.prototype.getTarget = function() {
	return dk.wildside.objectManager.fetch(this.target);
};

dk.wildside.Event.prototype.setTarget = function(target) {
	this.target = dk.wildside.objectManager.register(target);
};

dk.wildside.Event.prototype.getCurrentTarget = function() {
	return dk.wildside.objectManager.fetch(this.currentTarget);
};

dk.wildside.Event.prototype.setCurrentTarget = function(target) {
	this.currentTarget = dk.wildside.objectManager.register(target);
};