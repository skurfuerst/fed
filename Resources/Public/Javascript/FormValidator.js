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
		var data = {};
		data[json.prefix] = {
			data: FED.FormValidator.getData(form, json)
		};
		var result = jQuery.ajax({
			type: 'post',
			dataType: 'json',
			async: true,
			url: json.link,
			data: data,
			complete: function(response, status) {
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
		for (var error in errors) {
			var fieldName =	config.prefix + '[' + config.objectName + '][' + errors[error].name + ']';
			var field = jQuery('[name="' + fieldName + '"]');
			if (field) {
				field.addClass('f3-form-error');
			}
		};
	},

	getData : function(form, config) {
		var fieldName =	config.prefix + '[__hmac]';
		var hmac = form.find('[name="' + fieldName + '"]').val();
		var serialized = hmac.substring(-40);
		var unserialized = unserialize(serialized);
		var data = {};
		for (var property in unserialized[config.objectName]) {
			var fieldNameSelector = config.prefix + '[' + config.objectName + '][' + property + ']';
			var field = form.find('[name="' + fieldNameSelector + '"]');
			data[property] = field.val();
		};
		return data;
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