




dk.wildside.display.field.Sanitizer = {
	
	noop : function(value) {
		return value;
	},
	
	integer : function(value) {
		value = value.toString();
		value = value.replace(/[^0-9]/g, '');
		if (!value) value = 0;
		return value;
	},
	
	trim : function(value) {
		return value.trim();
	},
	
	float : function(value) {
		value = value.toString();
		if (!/^[0-9]{0,}[\.,]{0,1}[0-9]{0,}$/.test(value)) {
			value = "0";
		};
		return value;
	},
	
	string : function(value) {
		
		return value;
	},
	
	preg : function(value, preg) {
		
		return value;
	}
	
};

