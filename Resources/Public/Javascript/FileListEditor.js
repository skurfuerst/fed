if (typeof FED == 'undefined') {
	var FED = {};
};

FED.FileListEditor = {

	onFileUploaded: function(up, file, info) {
		var response = jQuery.parseJSON(info.response).result;
		file.name = response.name;
		FED.FileListEditor.addFileToSavedList(file, info);
		up.removeFile(file);
		return true;
	},

	addFileToSavedList: function(file, info) {
		if (file instanceof Array) {
			if (file.length == 0) {
				return false;
			};
			var index = 0;
			while (index < file.length) {
				FED.FileListEditor.addFileToSavedList(file[index], info);
				index++;
			};
			return true;
		};
		jQuery('#pleditor').append("<tr class='plupload_delete ui-state-default plupload_file'>" +
			"<td class='plupload_cell plupload_file_name'>" + file.name + "</td>" +
			"<td class='plupload_cell plupload_file_status'>Uploaded</td>" +
			"<td class='plupload_cell plupload_file_size'>" + plupload.formatSize(file.size) + "</td>" +
			"<td class='plupload_cell'><div class='ui-icon ui-icon-circle-minus remove'></div></td>" +
			"</tr>");
		if (!file.existing) {
			var files = FED.FileListEditor.getFieldValue();
			files.push(file.name);
			FED.FileListEditor.setFieldValue(files);
		};
		return true;
	},

	removeFileFromSavedList: function() {
		var row = jQuery(this).parents('tr:first');
		var filename = row.find('.plupload_file_name').html().trim();
		if (filename.length < 1) {
			return false;
		};
		var index;
		var updated = [];
		var existing = FED.FileListEditor.getFieldValue();
		for (index in existing) {
			if (existing[index] != filename) {
				updated.push(existing[index]);
			};
		};
		FED.FileListEditor.setFieldValue(updated);
		row.fadeOut(250);
		return true;
	},

	getFieldValue: function() {
		var field = jQuery('#plupload-field');
		var existing = field.val().split(',');
		return existing;
	},

	setFieldValue: function(files) {
		var field = jQuery('#plupload-field');
		var value = files.join(',');
		field.val(value);
	}

};

