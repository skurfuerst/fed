if (typeof FED == 'undefined') {
	FED = {};
};

FED.FormValidator = {

	forms : jQuery('.fed-validator'),

	fields : forms.find(':input, textarea'),

	validate : function() {
		var source = jQuery(this);
		var form = source.parents('form');
		var url = form.attr('rel');
		var result = jQuery.ajax({

		}).responseText;
		if (responseText == '1') {
			if (form.hasClass('.fed-autosubmit')) {
				form.submit();
			} else {
				return true;
			};
		} else {
			var data = jQuery.parseJSON(result);
			console.log(data);
		}
	},

	findFieldByName : function(form, fieldName) {

	}

};