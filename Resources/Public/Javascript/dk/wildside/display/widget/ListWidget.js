// Widget to render list-style displays by calling various input control methods.
// Dispatches events from clicked members which can be listened to by outside 
// Widgets/Components.
// Should be named using setName(myUniqueName) or through a ViewHelper; can then be referenced 
// by any parent Widget/Component by calling this.children.find(myUniqueName);

dk.wildside.display.widget.ListWidget = function(jQueryElement) {
	if (typeof jQueryElement == 'undefined') {
		return this;
	};
	dk.wildside.display.widget.Widget.call(this, jQueryElement);
	this.id = parseInt(Math.random()*100);
	var widget = this;
	this.addEventListener(dk.wildside.event.MouseEvent.CLICK, this.onClick);
	this.context.find(".wildside-extbase-sprite:not(." + this.selectors.inUse +")").each(function() {
		var obj = jQuery(this);
		var sprite = dk.wildside.spawner.get(this);
		widget.addChild.call(widget, sprite);
	});
};



dk.wildside.display.widget.ListWidget.prototype = new dk.wildside.display.widget.Widget();

dk.wildside.display.widget.ListWidget.prototype.getValue = function() {
	//console.log('Asked for value');
};

dk.wildside.display.widget.ListWidget.prototype.getMembers = function() {
	var members = new dk.wildside.util.Iterator();
	this.children.each(function(memberSprite) {
		var member = {
			value : memberSprite.getValue(),
			name : memberSprite.getName()
		};
		members.push(member);
	});
	return members;
}

dk.wildside.display.widget.ListWidget.prototype.checkMember = function(member) {
	if (typeof member != 'object') {
		console.info('Member must be an object.');
		return false;
	};
	if (!member.name) {
		member.name = member.value;
	};
	if (!member.name || !member.value) {
		console.info('Member must have both .value and .name properties');
	};
	return true;
};

dk.wildside.display.widget.ListWidget.prototype.checkMembers = function(members) {
	if (typeof members != 'array' && members instanceof dk.wildside.util.Iterator == false) {
		console.info('ListWidget does not know what to do with this value:');
		console.warn(members);
		return false;
	};
	return true;
};

dk.wildside.display.widget.ListWidget.prototype.addMember = function(member) {
	if (!this.checkMember(member)) {
		console.log('Trying to add invalid member');
		console.warn(member);
		return;
	};
	var html = "<div class='wildside-extbase-sprite'><div class='wildside-extbase-json'>" +
			"{\"displayType\":\"dk.wildside.display.Sprite\",\"name\":\""+member.name+"\",\"value\":"+member.value+"}" +
			"</div><div class='list-item'>" + member.name + "</div></div>";
	this.context.find('.list-container').append(html);
	var context = this.context.find(".wildside-extbase-sprite:not(." + this.selectors.inUse +")");
	var sprite = dk.wildside.spawner.get(context);
	this.addChild(sprite);
};

dk.wildside.display.widget.ListWidget.prototype.removeMember = function(member) {
	var sprite = this.children.find(member.name);
	if (sprite) {
		this.children = this.children.remove(sprite);
		sprite.remove();
	} else {
		console.log('Tried to remove illegal member');
		return false;
	};
};

dk.wildside.display.widget.ListWidget.prototype.addMembers = function(members) {
	if (!this.checkMembers(members)) {
		return;
	};
	var iterator = new dk.wildside.util.Iterator().merge(members);
	var parent = this;
	iterator.each(function(member) { parent.addMember.call(parent, member); });
};

dk.wildside.display.widget.ListWidget.prototype.removeMembers = function(members) {
	if (!this.checkMembers(members)) {
		return;
	};
	var iterator = new dk.wildside.util.Iterator().merge(members);
	var parent = this;
	iterator.each(function(member) { parent.removeMember.call(parent, member); });
};

dk.wildside.display.widget.ListWidget.prototype.removeAllMembers = function() {
	
};

dk.wildside.display.widget.ListWidget.prototype.onClick = function(event) {
	event.cancelled = false;
	event.currentTarget = this;
	event.type = dk.wildside.event.widget.ListWidgetEvent.MEMBER_REMOVED;
	this.dispatchEvent(event);
};

// NOTE: this function should analyze the event.originalEvent to determine which member was clicked
dk.wildside.display.widget.ListWidget.prototype.onSelectMember = function(event) {
	//console.info('Member clicked, event:');
	//console.warn(event);
};