/*!
 * Модуль "Справочная информация".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

/**
 * @class Gm.be.guide.DataModel
 * @extends Ext.data.Model
 */
Ext.define('Gm.be.guide.DataModel', {
    extend: 'Ext.data.Model',
    fields: ['text', 'doc']
});


/**
 * @class Gm.be.guide.TreePanel
 * @extends Ext.tree.Panel
 */
Ext.define('Gm.be.guide.TreePanel', {
    extend: 'Ext.tree.Panel',
    xtype: 'gm-guide-tree',
    cls: 'gm-guide-tree',
    controller: 'navigator',
    frameConfig: {},
    animate: false,
    useArrows: true,
    multiSelect: true,
    singleExpand: true,
    rootVisible: false,
    listeners: {
        itemclick: function (tree, record, item, index, e, eOpts) {
            if (!record.data.leaf) return false;
            var c = tree.panel.frameConfig;
            Ext.getCmp(c.id).loadSrc(c.url + record.data.doc + '.html' + c.params);
        }
    },
    tools: [{
         type:'refresh',
         handler: function (event, toolEl, panelHeader) {
            var tree = panelHeader.ownerCt,
                store = tree.getStore();
            store.getRootNode().removeAll();
            tree.mask();
            Ext.Ajax.request({
                url: Gm.url.build(tree.frameConfig.nodesUrl),
                method: 'get',
                success: function(response){
                    tree.unmask();
                    var response = Gm.response.normalize(response);
                    if (!response.success) {
                        Ext.Msg.exception(response, false, true);
                        return;
                    }
                    store.reload();
                    store.getRootNode().appendChild(response.data);
                },
                failure: function(response) {
                    tree.unmask();
                    Ext.Msg.exception(response, true, true);
                }
            });
        }
    }]
});
