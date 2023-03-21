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

use Ivory\GoogleMap\Helper\PlaceAutocompleteHelper as BasePlaceAutocompleteHelper;
use Ivory\GoogleMap\Place\Autocomplete;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PlaceAutocompleteHelper extends Helper
{
    /**
     * @var BasePlaceAutocompleteHelper
     */
    private $placeAutocompleteHelper;

    public function __construct(BasePlaceAutocompleteHelper $placeAutocompleteHelper)
    {
        $this->placeAutocompleteHelper = $placeAutocompleteHelper;
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

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'ivory_google_place_autocomplete';
    }
}
