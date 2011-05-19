

dk.wildside.util.Iterator = function(length) {
	try {
		Array.apply(this, length);
	} catch (e) {};
	return this;
};

dk.wildside.util.Iterator.prototype = new Array();

dk.wildside.util.Iterator.prototype.find = function(name) {
	var func = function(item, iteration) {
		try {
			if (typeof item.name != 'undefined' && item.name == name) {
				return true;
			};
			return (item.getName() == name);
		} catch (e) {
			return false;
		};
	};
	return this.filter(func).shift();
};

dk.wildside.util.Iterator.prototype.filter = function(func) {
	
	var returnData = new dk.wildside.util.Iterator();
	
	jQuery.each(this, function(iteration, data) {
		
		// Run the passed function with "this" as scope, and store its return value
		var tempvalue = func.call(this, data, iteration);
		
		// If the returnvalue strict-matches true, save it in the return data.
		if (tempvalue === true) {
			returnData.push(data);
		};
	});
	
	// Return only matching data.
	return returnData;
	
};

dk.wildside.util.Iterator.prototype.each = function(func) {
	
	// Run the passed function ("func"), with "this" as scope, on each element in the
	// object. Please note that the order of the objects is reversed compared to
	// the jQuery norm, so func is called by: func.call(this, data, iteration);
	jQuery.each(this, function(iteration, data) {
		func.call(this, data, iteration);
	});
	
	// Return the original gangsta for chaining goodness.
	return this;
};


dk.wildside.util.Iterator.prototype.copy = function() {
	var copy = new dk.wildside.util.Iterator();
	this.each(function(element) { copy.push(element); });
	return copy;
};

dk.wildside.util.Iterator.prototype.toArray = function() {
	var arr = new Array();
	this.each(function(element) { arr.push(element); });
	return arr;
};

dk.wildside.util.Iterator.prototype.remove = function(arg) {
	// Use internal filter to return everything that DOESN'T match the argument passed along
	var result = this.filter( function(data) { return (data != arg); });
	return result;
};

dk.wildside.util.Iterator.prototype.removeByContext = function(arg) {
	// Use internal filter to return everything that DOESN'T match the argument passed along
	var result = this.filter( function(data) { return (data.context != arg.context); });
	return result;
};

dk.wildside.util.Iterator.prototype.contains = function(arg) {
	// Use internal filter to count the entries that match the argument passed along.
	return this.filter(function(data){ return (data == arg); }).length;
};


dk.wildside.util.Iterator.prototype.merge = function(arr) {
	for (i = 0; i < arr.length; i++) {
		this.push(arr[i]);
	};
	return this;
};


dk.wildside.util.Iterator.prototype.first = function() {
	return this[0];
};

dk.wildside.util.Iterator.prototype.last = function() {
	return this[this.length - 1];
};

dk.wildside.util.Iterator.prototype.clear = function() {
	for (i = 0; i < this.length; i++) {
		delete(this[i]);
	};
	return this;
};