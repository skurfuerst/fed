/***************************************************************
* RecordSelectorWidget - Gui Object
* 
* Highly configurable Record Selector - acts as a regular Widget
* but allows you to manipulate the value of a field (or more fields 
* if you subclass this class) by selecting records from the 
* database through a list/search/order/add/remove-type interface
* 
***************************************************************/

dk.wildside.display.widget.RecordSelectorWidget = function(jQueryElement) {
	if (typeof jQueryElement == 'undefined') {
		return this;
	};
	dk.wildside.display.widget.Widget.call(this, jQueryElement);
	
	this.identity = 'recordselector-widget';
	this.searchTimer = false;
	this.hasSearched = false;
	this.resultList = this.children.find('results');
	this.memberList = this.children.find('selections');
	
	// use the DIRTYFIELD listener to determine which action was requested (by which Field triggered the event)
	this.addEventListener(dk.wildside.event.FieldEvent.KEYPRESS, this.onDirtyField);
	// make the widget itself listen for requests to start searching - these can come from the outside too
	this.addEventListener(dk.wildside.event.widget.RecordSelectorEvent.SEARCH, this.onSearch);
	// listen to own Events of type 'results received' (response containing listable results data)
	this.addEventListener(dk.wildside.event.widget.RecordSelectorEvent.RESULT, this.onResult);
	// listen to own Events of type 'selection clicked' (can be an icon inside selection, which triggers the event - could use to remove, edit, sort etc)
	this.addEventListener(dk.wildside.event.widget.ListWidgetEvent.MEMBER_ADDED, this.onAdd);
	// listen to own Events of type 'selection removed' (will fire when an entry is removed; the Event will contain a reference to what was removed)
	this.addEventListener(dk.wildside.event.widget.ListWidgetEvent.MEMBER_REMOVED, this.onRemove);
	
	this.resultList.hide();
	
	//console.info(this.context);
};


dk.wildside.display.widget.RecordSelectorWidget.prototype = new dk.wildside.display.widget.Widget();



// DATA MANIPULATION METHODS
/*
dk.wildside.display.widget.RecordSelectorWidget.setValue = function() {
	// setting the value should analyze the current list of values, ignore existing, 
	// remove entries not in the new value and finally call selectResult(resultUid) on 
	// each new entry - to allow onAdd events to fire, in case a subclasser wants to 
	// perform custom actions such as resolving additional information or setting a 
	// special class on selected new additions
};
*/
dk.wildside.display.widget.RecordSelectorWidget.prototype.getValue = function() {
	var value;
	if (this.dataType == '1:1') {
		return this.memberList.getChildAt(0).getValue();
	}
	value = [];
	this.memberList.children.each(function(memberSprite) {
		value.push(memberSprite.getValue());
	});
	if (this.config.dataType == '1:n') {
		value = value.join(',');
	};
	return value;
};





dk.wildside.display.widget.RecordSelectorWidget.prototype.doSearch = function() {
	clearTimeout(this.searchTimer);
	var data = {
		"q" : this.children.find('q').getValue(),
		"table" : this.config.table,
		"titleField" : this.config.titleField,
		"storagePid" : this.config.storagePid
	};
	var request = new dk.wildside.net.Request(this, this.config.action);
	request.setData(data);
	var results = this.dispatchRequest(request, false).data;
	var currentMembers = this.resultList.getMembers();
	this.resultList.removeMembers(currentMembers);
	for (var uid in results) {
		var member = {
			value : uid,
			name : results[uid]
		};
		this.resultList.addMember.call(this.resultList, member);
	};
}








// EVENT LISTENER METHODS
dk.wildside.display.widget.RecordSelectorWidget.prototype.onDirtyField = function(event) {
	// TODO: change this from a wrapper into a single listener - if no other Fields except for 'q' are needed
	event.cancelled = true;
	var field = event.getTarget();
	var eventType = 'unknown';
	var parent = this.getParent();
	switch (field.getName()) {
		case 'q':
			eventType = dk.wildside.event.widget.RecordSelectorEvent.SEARCH;
			break;
		default:
			eventType = dk.wildside.event.FieldEvent.DIRTY;
			return parent.dispatchEvent.call(parent, eventType);
			break;
	};
	this.dispatchEvent(eventType);
};

dk.wildside.display.widget.RecordSelectorWidget.prototype.onSearch = function(event) {
	clearTimeout(this.searchTimer);
	var queryString = this.children.find('q').getValue();
	if (queryString.length < 3) {
		//console.warn('Waiting for string length of 3: ' + queryString.length.toString());
		return;
	};
	var issuer = this;
	setTimeout(function() {
		issuer.doSearch.call(issuer);
	}, 500);
	event.cancelled = true;
	this.resultList.show();
};

dk.wildside.display.widget.RecordSelectorWidget.prototype.onResult = function(event) {
	
	
	this.hasSearched = false;
	event.cancelled = true;
};

dk.wildside.display.widget.RecordSelectorWidget.prototype.onSelect = function(event) {
	
};

dk.wildside.display.widget.RecordSelectorWidget.prototype.onAdd = function(event) {
	// suppressed; not used at this time
	return;
};

dk.wildside.display.widget.RecordSelectorWidget.prototype.onDirty = function(event) {
	// suppressed; not used at this time
	return;
};

dk.wildside.display.widget.RecordSelectorWidget.prototype.sync = function() {
	// suppressed; not used at this time
	return;
};


dk.wildside.display.widget.RecordSelectorWidget.prototype.onRemove = function(event) {
	// NOTE: this function grabs "removed" members from both ListWidgets,
	// determines what to do (add member, remove member) by which list was clicked
	// - either memberList or resultList
	var memberSprite = event.getTarget();
	var listWidget = memberSprite.getParent();
	var member = {
		name : memberSprite.getName(),
		value : memberSprite.getValue()
	};
	if (listWidget.getName() == 'selections') {
		// action is remove.
		this.memberList.removeMember(member);
		if (this.config.preload || this.hasSearched == true) {
			this.resultList.addMember(member); // return to pool
		} else {
			this.resultList.hide();
		}
	} else if (listWidget.getName() == 'results') {
		// action is to remove from results (already happened in ListWidget) and add to selections:
		this.memberList.addMember(member);
		this.resultList.removeMember(member);
	};
	this.dispatchEvent(dk.wildside.event.FieldEvent.DIRTY);
	event.cancelled = true;
};

dk.wildside.display.widget.RecordSelectorWidget.prototype.onSort = function(event) {
	
};