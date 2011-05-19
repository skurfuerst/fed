/***************************************************************
* DatePickerWidget - Gui Object
* 
* WSAPI interface for jQuery date picker
* 
***************************************************************/

dk.wildside.display.widget.DatePickerWidget = function(jQueryElement) {
	
	dk.wildside.display.widget.Widget.call(this, jQueryElement);
	
	this.dateFormat = this.config.data.dateFormat;
	
	var p = this;
	
	// Bootstrap all date fields found below this widget
	this.context.find(":input.date-field").not(".hasDatepicker").datepicker({ dateFormat : p.dateFormat });
	
};

dk.wildside.display.widget.DatePickerWidget.prototype = new dk.wildside.display.widget.Widget();


/*
dk.wildside.display.widget.DatePickerWidget.prototype.setValue = function(value) {
	//this.fields[0].setValue(value);
	this.value = value;
};
*/

dk.wildside.display.widget.DatePickerWidget.prototype.onDirtyField = function(event) {
	event.cancelled = true;
	this.dispatchEvent(dk.wildside.event.widget.WidgetEvent.DIRTY);
};


dk.wildside.display.widget.DatePickerWidget.prototype.getValue = function() {
	
	// This function reads the formatted string from the field, and returns a UNIX timestamp instead,
	// as long as the input format matches dateFormat (as set in the widget's configuration).
	var tmpVal = this.context.find(":input").val();
	var elapsedMS = 0;
	// try/catch to weed out formatting errors and stuff.
	try {
		var rawVal = new Date(jQuery.datepicker.parseDate(this.dateFormat, tmpVal));
		var elapsedMS = rawVal.getTime();
		if (elapsedMS < 0) elapsedMS = 0;
		elapsedMS /= 1000;
	} catch(e) {
		elapsedMS = 0;
	};
	return elapsedMS;

};