<?php

/*
 * This file is part of the Ivory Google Map bundle package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMapBundle\Templating;

use Ivory\GoogleMap\Helper\MapHelper as BaseMapHelper;
use Ivory\GoogleMap\Map;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MapHelper extends Helper
{
    /**
     * @var BaseMapHelper
     */
    private $mapHelper;

    /**
     * @param BaseMapHelper $mapHelper
     */
    public function __construct(BaseMapHelper $mapHelper)
    {
        $this->mapHelper = $mapHelper;
    }

    /**
     * @param Map      $map
     * @param string[] $attributes
     */
    public function render(Map $map, array $attributes = []): string
    {
        $map->addHtmlAttributes($attributes);

        return $this->mapHelper->render($map);
    }

    /**
     * @param Map      $map
     * @param string[] $attributes
     */
    public function renderHtml(Map $map, array $attributes = []): string
    {
        $map->addHtmlAttributes($attributes);

        return $this->mapHelper->renderHtml($map);
    }

    public function renderStylesheet(Map $map): string
    {
        return $this->mapHelper->renderStylesheet($map);
    }

    public function renderJavascript(Map $map): string
    {
        return $this->mapHelper->renderJavascript($map);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'ivory_google_map';
    }
}
