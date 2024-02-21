<?php

/*
 * This file is part of the Ivory Google Map bundle package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMapBundle\Twig;

use Ivory\GoogleMap\Helper\StaticMapHelper;
use Ivory\GoogleMap\Map;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class StaticMapExtension extends AbstractExtension
{
    public function __construct(private StaticMapHelper $staticMapHelper)
    {}

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $functions = [];

        foreach ($this->getMapping() as $name => $method) {
            $functions[] = new TwigFunction($name, [$this, $method], ['is_safe' => ['html']]);
        }

        return $functions;
    }

    public function render(Map $map): string
    {
        return $this->staticMapHelper->render($map);
    }

    public function getName(): string
    {
        return 'ivory_google_map_static';
    }

    /**
     * @return string[]
     */
    private function getMapping(): array
    {
        return ['ivory_google_map_static' => 'render'];
    }
}
