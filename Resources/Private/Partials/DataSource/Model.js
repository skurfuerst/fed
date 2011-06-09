{namespace fed=Tx_Fed_ViewHelpers}

Ext.define('{prefix}{className}', {
    extend: 'Ext.data.Model',
	fields: [
	<f:for each="{properties}" as="def" key="name" iteration="iteration">
        {name: '{def.name}', type: '{def.type}', xtype: 'textfield' }
		<f:if condition="{iteration.isLast}" then="" else="," />
	</f:for>
	],
	idProperty: 'uid',
    proxy: {
        type: 'rest',
		api: {
			create: '<f:format.raw>{urls.create}</f:format.raw>',
			read: '<f:format.raw>{urls.read}</f:format.raw>',
			update: '<f:format.raw>{urls.update}</f:format.raw>',
			destroy: '<f:format.raw>{urls.destroy}</f:format.raw>'
		}
	}
});
