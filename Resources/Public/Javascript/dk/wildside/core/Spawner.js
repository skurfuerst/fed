


dk.wildside.core.Spawner = function() {
	
};

dk.wildside.core.Spawner.prototype.get = function(jQueryElement) {
	if (typeof jQueryElement.jquery == 'undefined') {
		jQueryElement = jQuery(jQueryElement);
	};
	var selectors = dk.wildside.util.Configuration.guiSelectors;
	var config = jQuery.parseJSON(jQueryElement.find('.' + selectors.json + ':first').html());
	var displayType = config.displayType;
	var spawnedObject = false;
	jQueryElement.data('config', config);
	if (typeof displayType == 'string') {
		eval("if (typeof(" + displayType + ") != 'undefined') spawnedObject = new " + displayType + "(jQueryElement);");
	};
	if (!spawnedObject) {
		//console.warn('Invalid displayType: ' + displayType);
		//console.warn(jQueryElement);
	};
	return spawnedObject;
};