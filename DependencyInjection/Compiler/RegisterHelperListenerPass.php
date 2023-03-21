<?php

/*
 * This file is part of the Ivory Google Map bundle package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterHelperListenerPass implements CompilerPassInterface
{
    /**
     * @var string[]
     */
    private static $helpers = [
        'api',
        'map',
        'map.static',
        'place_autocomplete',
    ];

    /**
     * @var RegisterListenersPass[]
     */
    private $passes = [];

    public function __construct()
    {
        foreach (self::$helpers as $helper) {
            $this->passes[] = new RegisterListenersPass(
                'ivory.google_map.helper.'.$helper.'.event_dispatcher',
                'ivory.google_map.helper.'.$helper.'.listener',
                'ivory.google_map.helper.'.$helper.'.subscriber'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($this->passes as $pass) {
            $pass->process($container);
        }
    }
}
