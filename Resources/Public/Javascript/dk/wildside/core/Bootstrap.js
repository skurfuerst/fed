dk.wildside.core.Bootstrap = function() {
	
};

dk.wildside.core.Bootstrap.prototype.run = function() {
	
	dk.wildside.config = {};
	dk.wildside.bootstrap = this;
	dk.wildside.spawner = new dk.wildside.core.Spawner();
	dk.wildside.objectManager = new dk.wildside.core.ObjectManager();
	
	// Find the settings-div, which is somewhere near the top of the page, and read all global settings
	// from it. These will be stored in the global object Locus.configuration.
	jQuery(dk.wildside.util.Configuration.guiSelectors.bootstrapConfiguration).each(function(){
		var setting = jQuery(this);
		var key = setting.attr("title");
		var value = setting.html().trim();
		dk.wildside.util.Configuration[key] = value; // TODO: when all references are gone, remove this
		dk.wildside.config[key] = value; // TODO: this ad hoc storage is to take the place of the singleton above
	});
	
	// Now, bootstrap all existing components on the page. This automatically handles
	// any sub-widgets found in there too.
	jQuery("." + dk.wildside.util.Configuration.guiSelectors.component)
		.not(jQuery("." + dk.wildside.util.Configuration.guiSelectors.component + " ." + dk.wildside.util.Configuration.guiSelectors.component))
		.each( function() {
			var component = dk.wildside.spawner.get(this);
		});
	
	// Now, if any widgets are left untouched, we need to bootstrap them as stand-alone
	jQuery("." + dk.wildside.util.Configuration.guiSelectors.widget +":not(." + dk.wildside.util.Configuration.guiSelectors.inUse +")").each( function() {
		var widget = dk.wildside.spawner.get(this);
	});
	
	// Basic configuration - this can be overruled later, though.
	var editConfig = { };
	jQuery("." + dk.wildside.util.Configuration.guiSelectors.alohaRule).each(function(){
		var t = jQuery(this);
		var key = t.attr("title");
		var val = t.text().trim().split(",");
		editConfig[key] = val;
	});
	
};