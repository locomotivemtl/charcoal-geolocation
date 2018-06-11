<?php

namespace Charcoal\Geolocation\Property\Input;

/**
 * Point Widget Input
 */
class PointWidgetInput extends MapWidgetInput
{
    /**
     * @return boolean
     */
    public function showMapTools()
    {
        return false;
    }
}
