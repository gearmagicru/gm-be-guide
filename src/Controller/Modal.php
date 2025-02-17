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
use Gm\View\View;
use Gm\Helper\Html;
use Gm\View\ClientScript;
use Gm\Panel\Http\Response;
use Gm\Panel\Widget\Window;
use Gm\Panel\Controller\BaseController;

/**
 * Контроллер модального окна справки.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Guide\Controller
 * @since 1.0
 */
class Modal extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['verb']['actions'] = [
            'view' => ['POST', 'ajax' => 'GJAX'],
            'data' => ['GET']
        ];
        return $behaviors;
    }

    /**
     * Создаёт виджет окна справки.
     * 
     * @return Window
     */
    public function createWidget(): Window
    {
        /** @var Window $widget Окно компонента (Ext.window.Window Sencha ExtJS) */
        $widget = new Window([
            'xtype'       => 'window',
            'id'          => $this->module->viewId('window'),
            'title'       => $this->t('Guide'),
            'cls'         => 'gm-guide-popup',
            'ui'          => 'popup',
            'anchorTo'    => 'right',
            'layout'      => 'anchor',
            'width'       => 350,
            'modal'       => false,
            'fixed'       => true,
            'maximizable' => true,
            'controller'  => 'gm-be-guide-modal'
        ]);
        $widget->addCss('/guide.css');
        $widget
            ->setNamespaceJS('Gm.be.guide')
            ->addRequire('Gm.be.guide.ModalController');
        return $widget;
    }
 
    /**
     * @param string $page
     * 
     * @return string
     */
    public function page(string $page)
    {
        return $page;
    }

    /**
     * Действие "data" выводит шаблона справки.
     * 
     * @return Response
     */
    public function dataAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        $response->setFormat($response::FORMAT_HTML);
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;
        /** @var array Ошибки */
        $errors = [];

        /** @var string|null $componentSignId Сигнатура компонента */
        $componentSignId = $request->getQuery('component');
        /** @var string|null $subject Тема справки */
        $subject = $request->getQuery('subject');
        /** @var string Содержимое справки */
        $content = '';

        if ($componentSignId) {
            if ($subject) {
                $chunks = explode(':', $componentSignId);
                $componentType = $chunks[0];
                $componentId   = $chunks[1] ?? '';
                if ($componentType && $componentId) {
                    switch ($componentType) {
                        case 'module': $manager = Gm::$app->modules; break;
                        case 'extension': $manager = Gm::$app->extensions; break;
                        case 'widget': $manager = Gm::$app->widgets; break;
                        case 'plugin': $manager = Gm::$app->plugins; break;
                        
                        default:
                            $manager = null;
                    }
                    if ($manager) {
                        /** @var array|null $component */
                        $component = $manager->getRegistry()->get($componentId);
                        if ($component) {
                            $filename = $manager->getHelpFile($componentId, $subject);
                            if ($filename) {
                                $view = new View();
                                $content = $view->renderFile($filename, [
                                    'assetsUrl' => Gm::$app->moduleUrl . $component['path'] . '/assets',
                                    'themeUrl'  => Gm::$app->theme->url,
                                    'url'       => function (string $subject, string $component = null) use ($componentSignId): string {
                                        if ($component === null) {
                                            $component = $componentSignId;
                                        }
                                        return '/' . Gm::alias('@match', "/modal/data?component=$component&subject=$subject");
                                    }
                                ]);
                            } else {
                                $view = new View([
                                    'useTheme'    => false,
                                    'useLocalize' => true
                                ]);
                                $content = $view->loadFile('404');
                            }
                        } else
                            $errors[] = Gm::t(BACKEND, 'Invalid argument "{0}"', ['component']);
                    } else
                        $errors[] = Gm::t(BACKEND, 'Invalid argument "{0}"', ['component']);
                } else 
                    $errors[] = Gm::t(BACKEND, 'Invalid argument "{0}"', ['component']);
            } else
                $errors[] = Gm::t(BACKEND, 'Invalid argument "{0}"', ['subject']);
        } else
            $errors[] = Gm::t(BACKEND, 'Invalid argument "{0}"', ['component']);

        // регистрация JS и CSS пакетов
        Gm::$app->clientScript
            ->appendPackages([
                'help' => [
                    'position' => ClientScript::POS_HEAD,
                    'theme'    => true,
                    'css'      => [
                        'bootstrap.min.css'   => ['/vendors/bootstrap/css/bootstrap.min.css'],
                        'robotocondensed.css' => ['/assets/fonts/robotocondensed.css'],
                        'icons.css'           => [ '/assets/icons/icons.css'],
                        'guide.css'           => ['/widgets/guide.css']
                    ]
                ]
            ])
            ->registerPackages('help');

        return $response->setContent(
            $this->renderLayout('help', ['errors' => $errors, 'content' => $content])
        );
    }

    /**
     * Действие "view" выводит интерфейс модального окна справки.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

        /** @var string|null Сигнатура компонента */
        $component = $request->getQuery('component');
        if (empty($component)) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Invalid argument "{0}"', ['component']));
            return $response;
        }

        /** @var string|null Тема справки */
        $subject = $request->getQuery('subject');
        if (empty($subject)) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Invalid argument "{0}"', ['subject']));
            return $response;
        }

        /** @var Window|false $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании виджета
        if ($widget === false) {
            return $response;
        }

        $widget->dockedItems = [
            'dock'  => 'top',
            'xtype' => 'toolbar',
            'items' => [
                '->',
                [
                    'xtype'   => 'button',
                    'icon'    => $widget->imageSrc('/icon-btn-home.png'),
                    'margin'  => '2px',
                    'handler' => 'clickHome',
                    'handlerArgs' => [
                        'url' => '/' . Gm::alias('@backend', '/guide/modal/data?component=' . $component . '&subject=index')
                    ]
                ]
            ]
        ];
        $widget->html = Html::iframe([
            'id'          => 'gm-guide__iframe',
            'class'       => 'g-frame_fit',
            'frameborder' => '0',
            'src'         => '/' . Gm::alias('@match', "/modal/data?component=$component&subject=$subject")
        ]);
        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }
}
