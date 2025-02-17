<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации установки модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'use'         => BACKEND,
    'id'          => 'gm.be.guide',
    'name'        => 'Guide', 
    'description' => 'User guide information',
    'namespace'   => 'Gm\Backend\Guide',
    'path'        => '/gm/gm.be.guide',
    'route'       => 'guide',
    'routes'      => [
        [
            'type'    => 'crudSegments',
            'options' => [
                'module'   => 'gm.be.guide',
                'route'    => 'guide',
                'prefix'   => BACKEND,
                'defaults' => [
                    'controller' => 'docs',
                    'action'     => 'view',
                ]
            ]
        ]
    ],
    'locales'     => ['ru_RU', 'en_GB'],
    'permissions' => ['any', 'view', 'read', 'settings', 'info'],
    'events'      => [],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'GM MS'],
        ['app', 'code' => 'GM CMS'],
        ['app', 'code' => 'GM CRM'],
    ]
];
