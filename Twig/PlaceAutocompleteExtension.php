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

use Ivory\GoogleMap\Helper\PlaceAutocompleteHelper;
use Ivory\GoogleMap\Place\Autocomplete;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PlaceAutocompleteExtension extends AbstractExtension
{
    public function __construct(private PlaceAutocompleteHelper $placeAutocompleteHelper)
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
     * @param string[]     $attributes
     */
    public function render(Autocomplete $autocomplete, array $attributes = []): string
    {
        $autocomplete->addInputAttributes($attributes);

        return $this->placeAutocompleteHelper->render($autocomplete);
    }

    /**
     * @param string[]     $attributes
     */
    public function renderHtml(Autocomplete $autocomplete, array $attributes = []): string
    {
        $autocomplete->addInputAttributes($attributes);

        return $this->placeAutocompleteHelper->renderHtml($autocomplete);
    }

    public function renderJavascript(Autocomplete $autocomplete): string
    {
        return $this->placeAutocompleteHelper->renderJavascript($autocomplete);
    }

    public function getName(): string
    {
        return 'ivory_google_place_autocomplete';
    }

    /**
     * @return string[]
     */
    private function getMapping(): array
    {
        return [
            'ivory_google_place_autocomplete'           => 'render',
            'ivory_google_place_autocomplete_container' => 'renderHtml',
            'ivory_google_place_autocomplete_js'        => 'renderJavascript',
        ];
    }
}
