<?php
/**
 * Модуль веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Guide;

/**
 * Модуль справки.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Guide
 * @since 1.0
 */
class Module extends \Gm\Panel\Module\Module
{
    /**
     * @var string Локальный путь к ресурсу справки.
     */
    public const baseGuideNodesUrl = '/output/tree.json';

    /**
     * {@inheritdoc}
     */
    public string $id = 'gm.be.guide';

    /**
     * Возращение URL-пути разделов справки.
     * 
     * @return string
     */
    public function getBaseGuideNodesUrl(): string
    {
        return self::baseGuideNodesUrl;
    }
}