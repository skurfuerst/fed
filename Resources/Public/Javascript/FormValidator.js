if (typeof FED == 'undefined') {
	FED = {};
};

FED.FormValidator = {

	forms : null,

	fields : null,

	validate : function() {
		var source = jQuery(this);
		var form = source.parents('form');
		var json = jQuery.parseJSON(form.attr('rel'));
		var dataSet = {};
		var collected = FED.FormValidator.getData(form, json);
		collected.action = json.action;
		dataSet[json.prefix] = {
			"data": collected
		};
		var result = jQuery.ajax({
			"type": 'post',
			"url": json.link,
			"data": dataSet,
			"complete": function(response, status) {
				var result = response.responseText;
				var data = jQuery.parseJSON(result);
				FED.FormValidator.cleanFields(form);
				if (result == '1') {
					if (json.autosubmit) {
						form.submit();
					} else {
						return true;
					};
				} else if (typeof data == 'object') {
					FED.FormValidator.highlightErrorFields(form, json, data);
				} else {
					console.warn('Unsupported return type: ' + typeof data);
				};
			}
		});
	},

	highlightErrorFields : function(form, config, errors) {
		for (var objectName in errors) {
			var propertyErrors = errors[objectName];
			for (var propertyName in propertyErrors) {
				var fieldName = config.prefix + '[' + objectName + '][' + propertyName + ']';
				var field = jQuery('[name="' + fieldName + '"]');
				if (field) {
					field.addClass('f3-form-error');
				};

			};
		};
	},

	getData : function(form, config) {
		var fieldName =	config.prefix + '[__hmac]';
		var hmac = form.find('[name="' + fieldName + '"]').val();
		var serialized = hmac.substring(-40);
		var unserialized = unserialize(serialized);
		var path = [];
		var data = this.getObjectData(form, unserialized, config.prefix, path);
		return data;
	},

	getObjectData : function(form, node, prefix, path, lastProperty) {
		var dataSet = {};
		var pathSet = [];
		for (var property in node) {
			path.push(property);
			if (typeof node[property] == 'object' || typeof node[property] == 'array') {
				dataSet[property] = this.getObjectData(form, node[property], prefix, path, property);
			} else {
				var value = this.getFieldValueByPath(prefix, path);
				if (parseInt(property) > 0 || property == '0') {
					if (parseInt(value) > 0) {
						dataSet[lastProperty] = this.getFieldValueByPath(prefix, jQuery(path).slice(0, path.length - 1));
					};
				} else {
					dataSet[property] = this.getFieldValueByPath(prefix, path);
				};
			};
			path.pop();
		};
		return dataSet;
	},

	getFieldValueByPath : function(prefix, path) {
		var name = prefix;
		for (var i=0; i<path.length; i++) {
			var part = path[i];
			name += '[' + part + ']';
		};
		var selector = '[name="' + name + '"]';
		var field = jQuery(selector);
		return field.val();
	},

	cleanFields : function(form) {
		form.find(':input, textarea').removeClass('f3-form-error');
	}

};

jQuery(document).ready(function() {
	FED.FormValidator.forms = jQuery('.fed-validator');
	FED.FormValidator.fields = FED.FormValidator.forms.find(':input, textarea');
	FED.FormValidator.fields.each(function() {
		jQuery(this).change(FED.FormValidator.validate);
	});
});