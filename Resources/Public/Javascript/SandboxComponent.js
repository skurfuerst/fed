Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.state.*'
]);

Ext.onReady(function() {

    var grid = Ext.create('Ext.grid.Panel', {
        height: 350,
        width: 1024,
        title: 'Array Grid',
        renderTo: 'fedsandboxcomponent',
        viewConfig: {
            stripeRows: true
        },
		plugins: [
			Ext.create('Ext.grid.plugin.RowEditing')
		],
		store: Ext.create('Ext.data.Store', {
			model: 'Fed.DataSource',
			autoLoad: true,
			autoSync: true
		}),
        columns: [
            {
                text     : 'Name',
                width	 : 100,
                sortable : false,
                dataIndex: 'name',
				field: {
					xtype: 'textfield'
				}
            },
            {
                text     : 'Description',
                flex	 : 1,
                sortable : true,
                dataIndex: 'description'
            },
			{
                text     : 'Query',
                flex	 : 1,
                sortable : true,
                dataIndex: 'query'
            },
			{
                text     : 'Data',
                flex	 : 1,
                sortable : true,
                dataIndex: 'data'
            }
        ],
		dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text: 'Add',
                iconCls: 'icon-add',
                handler: function(){
                    // empty record
                    store.insert(0, new Person());
                    rowEditing.startEdit(0, 0);
                }
            }, '-', {
                text: 'Delete',
                iconCls: 'icon-delete',
                handler: function(){
                    var selection = grid.getView().getSelectionModel().getSelection()[0];
                    if (selection) {
                        store.remove(selection);
                    }
                }
            }]
        }]
    });
});