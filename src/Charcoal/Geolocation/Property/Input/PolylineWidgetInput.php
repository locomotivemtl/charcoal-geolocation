<?php

namespace Charcoal\Geolocation\Property\Input;

/**
 * Point Widget Input
 */
class PolylineWidgetInput extends MapWidgetInput
{
    /**
     * @return boolean
     */
    public function showMapTools()
    {
        return false;
    }

    /**
     * Retrieve the default map widget options.
     *
     * @return array
     */
    public function defaultMapOptions()
    {
        return [
            'api_key' => $this->apiKey(),
            'multiple' => $this->multiple()
        ];
    }
}
