<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */
namespace Gm\Backend\Guide\Controller;

use Gm;
use Gm\Panel\Http\Response;
use Gm\Panel\Widget\TabGuide;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Controller\BaseController;

/**
 * Контроллер панели справки.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Guide\Controller
 * @since 1.0
 */
class Docs extends BaseController
{
    /**
     * @var BaseModule|\Gm\Backend\Guide\Module
     */
    public BaseModule $module;

    /**
     * Создаёт виджет вкладки справки.
     * 
     * @return TabGuide
     */
    public function createWidget(): TabGuide
    {
        /** @var TabGuide $tab */
        $tab = new TabGuide();
        $tab
            ->setNamespaceJS('Gm.be.guide')
            ->addRequire('Gm.be.guide.IFrame')
            ->addRequire('Gm.be.guide.Tree')
            ->addCss('/guide.css');
        return $tab;
    }

     /**
     * Действие "view" выводи интерфейса справки.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        // настройки справки
        $params   = '?view=article';
        $settings = $this->module->getSettings();
        if ($settings->url == null) {
            $response
                ->meta->error($this->t('It is impossible to gain access to the Help section'));
            return $response;
        }
        if ($settings->theme != null) {
            $params .= '&theme=' . $settings->theme;
        }
        $name = Gm::$app->request->getQuery('name', false);
        if ($name)
            $frameUrl = $settings->url . $name . '.html';
        else
            $frameUrl = $settings->url;

        // получение разделов справки
        $store = $this->module->getStorage();
        if ($store->guideTreeNodes != null) {
            $nodes = $store->guideTreeNodes;
        } else {
            /** @var \Gm\Backend\Guide\Model\TreeNodes $tree */
            $tree  = $this->getModel('TreeNodes');
            $nodes = $tree->getNodes();
        }
        if (empty($nodes)) {
            $response
                ->meta->error($this->t('It is impossible to gain access to the Help section') . ': ' . $settings->url . $this->module->getBaseGuideNodesUrl());
            return $response;
        }
        $store->guideTreeNodes = $nodes;

        /** @var TabGuide $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании виджета
        if ($widget === false) {
            return $response;
        }

        // разделы справки (Gm.view.guide.TreePanel GmJS)
        $widget->tree->frameConfig['url'] = $settings->url;
        $widget->tree->frameConfig['params'] = $params;

        // панель фрейма справки (Ext.Panel Sencha ExtJS)
        $widget->frame->src = $frameUrl . $params;

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }
}
