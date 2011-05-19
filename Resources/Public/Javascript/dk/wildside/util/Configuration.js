

dk.wildside.util.Configuration = {
	
	bootstrapper : 'dk.wildside.core.Bootstrap',
	
	// Debug tracing. Enable this to get console (firebug) output traces from the framework
	traceEnabled : true,
	
	// Selector names for objects. These are used by the entire system to add/remove and target
	// classnames on objects
	guiSelectors : {
		bootstrapConfiguration: ".settings > .setting",
		alohaRule : "fed-aloha-rule",
		json : "fed-json",
		widget : "fed-widget",
		field : "fed-field",
		component : "fed-component",
		inUse : "fed--inuse",
		jsParent : "fed--jsParent",
		instantiationFail : "fed--instantiationFail",
		jQueryDataName : "fed-data",
		messageDisplayElement : "fed-messages",
		messageClassInfo : "info",
		messageClassError : "error",
		itemParentLookup : ".plan.item",
		alohaConfigBasic : "noFormatting",
		alohaConfigFull : "fullEditor"
	}

};