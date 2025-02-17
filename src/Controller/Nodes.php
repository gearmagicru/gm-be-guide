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
use Gm\Panel\Controller\BaseController;

/**
 * Контроллер разделов (дерева) справки.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Guide\Controller
 * @since 1.0
 */
class Nodes extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'verb' => [
                'class'    => '\Gm\Filter\VerbFilter',
                'autoInit' => true,
                'actions'  => [
                    'data' => ['GET',  'ajax' => 'GJAX'],
                    '*'    => ['POST', 'ajax' => 'GJAX']
                ]
            ]
        ];
    }

    /**
     * Действие "data" выводит разделы справки.
     * 
     * @return Response
     */
    public function dataAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var \Gm\Backend\Guide\Model\TreeNodes $tree */
        $tree = $this->getModel('TreeNodes');
        return $response->setContent($tree->getNodes());
    }
}
