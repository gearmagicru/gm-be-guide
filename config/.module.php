<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'  => 'text-%s.php'
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для авторизованных пользователей Панели управления
        [ // разрешение "Полный доступ" (any: view, read)
            'allow',
            'permission'  => 'any',
            'controllers' => [
                'Docs'  => ['view'],
                'Modal' => ['view', 'data'],
                'Nodes' => ['data']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Просмотр" (view)
            'allow',
            'permission'  => 'view',
            'controllers' => [
                'Docs'  => ['view'],
                'Modal' => ['view', 'data'],
                'Nodes' => ['data']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Чтение" (read)
            'allow',
            'permission'  => 'read',
            'controllers' => [
                'Nodes' => ['data'],
                'Modal' => ['data']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Информация о модуле" (info)
            'allow',
            'permission'  => 'info',
            'controllers' => ['Info'],
            'users'       => ['@backend']
        ],
        [ // разрешение "Настройка модуля" (settings)
            'allow',
            'permission'  => 'settings',
            'controllers' => ['Settings'],
            'users'       => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny',
        ]
    ],

    'dataManager' => [
        'settings' => [
            'aliases' => [
                'url'   => ['url'],
                'theme' => ['theme'],
                'name'  => ['name'],
            ]
        ]
    ],

    'viewManager' => [
        'id'          => 'gm-guide-{name}',
        'useTheme'    => true,
        'useLocalize' => true,
        'viewMap'     => [
            // информации о модуле
            'info' => [
                'viewFile'      => '//backend/module-info.phtml', 
                'forceLocalize' => true
            ],
            'settings' => '/settings.json',
            'help'     => '/layouts/help.phtml'
        ]
    ]
];
