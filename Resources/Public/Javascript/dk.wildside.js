
if (typeof dk == 'undefined') {
	dk = {};
};

if (typeof dk.wildside == 'undefined') {
	dk.wildside = {};
	dk.wildside.bootstrap = {};
	dk.wildside.spawner = {};
	dk.wildside.objectManager = {};
	dk.wildside.core = {};
	dk.wildside.display = {};
	dk.wildside.display.widget = {};
	dk.wildside.display.field = {};
	dk.wildside.display.component = {};
	dk.wildside.event = {};
	dk.wildside.event.widget = {};
	dk.wildside.net = {};
	dk.wildside.util = {};
};

jQuery(document).ready(function() {
	eval('var bootstrap = new ' + dk.wildside.util.Configuration.bootstrapper + '();');
	bootstrap.run();
});

// util/String.js
// util/Iterator.js
// util/Configuration.js
// core/ObjectManager.js
// net/Request.js
// net/Response.js
// net/Responder.js
// net/Dispatcher.js
// event/Event.js
// event/FieldEvent.js
// event/MouseEvent.js
// event/EventDispatcher.js
// event/widget/WidgetEvent.js
// event/widget/ListWidgetEvent.js
// event/widget/FileUploadEvent.js
// event/widget/RecordSelectorEvent.js
// display/DisplayObject.js
// display/Sprite.js
// display/Control.js
// display/Component.js
// display/field/Sanitizer.js
// display/field/Field.js
// display/field/Aloha.js
// display/field/Input.js
// display/field/Message.js
// display/field/Checkbox.js
// display/field/Radio.js
// display/field/Select.js
// display/field/Button.js
// display/field/Textarea.js
// display/field/Value.js
// display/widget/Widget.js
// display/widget/ListWidget.js
// display/widget/DatePickerWidget.js
// display/widget/FileUploadWidget.js
// display/widget/RecordSelectorWidget.js
// display/widget/PDFWidget.js
// core/Spawner.js
// core/Bootstrap.js