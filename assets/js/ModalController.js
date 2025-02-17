/*!
 * Контроллер модального окна справки.
 * Модуль "Справочная информация".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.be.guide.ModalController', {
    extend: 'Gm.view.form.PanelController',
    alias: 'controller.gm-be-guide-modal',

    /**
     * Нажатие на кнопку "Содержание" (главная страница компонента).
     * @param {Ext.button.Button} btn
     * @param {Event} e
     * @param {Object} eOpts
     */
    clickHome: function (btn, e, eOpts ) {
        Ext.getDom('gm-guide__iframe').src = btn.handlerArgs.url;
    }
});