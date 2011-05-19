

dk.wildside.core.ObjectManager = function() {
	this.instances = [];
};

dk.wildside.core.ObjectManager.prototype.register = function(object) {
	try {
		var id = object.getId();
		if (id == false) {
			id = this.createId();
			object.setId(id);
		};
		this.instances[id] = object;
		return id;
	} catch (e) {
		console.warn('Error! Trying to register invalid object: ' + object);
	};
};

dk.wildside.core.ObjectManager.prototype.fetch = function(id) {
	if (typeof id == 'number') {
		return this.instances[id];
	} else if (typeof id == 'object') {
		try {
			return this.instances[id.getId()];
		} catch (e) {
			console.warn('Error! Trying to fetch invalid object: ' + id.toString());
		}
	} else {
		// puke up harbl. Don't know what to do. Harbl!
		console.info(typeof id);
		console.warn('Did not know what to do with argument for "fetch"');
		console.info(id);
	};
};

dk.wildside.core.ObjectManager.prototype.createId = function() {
	var id = parseInt(Math.random()*16768);
	if (typeof this.instances[id] == 'undefined') {
		return id;
	} else {
		return this.createId(); // keep on going until a free ID is randomized
	};
};
