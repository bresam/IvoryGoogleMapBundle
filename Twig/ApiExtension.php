<?php

/*
 * This file is part of the Ivory Google Api bundle package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMapBundle\Twig;

use Ivory\GoogleMap\Helper\ApiHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ApiExtension extends AbstractExtension
{
    public function __construct(private ApiHelper $apiHelper)
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

    /**
     * @param object[] $objects
     */
    public function render(array $objects): string
    {
        return $this->apiHelper->render($objects);
    }

    public function getName(): string
    {
        return 'ivory_google_api';
    }

    /**
     * @return string[]
     */
    private function getMapping(): array
    {
        return ['ivory_google_api' => 'render'];
    }
}
