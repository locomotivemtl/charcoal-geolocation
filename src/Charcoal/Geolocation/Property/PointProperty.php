<?php

namespace Charcoal\Geolocation\Property;

/**
 * MapStructure Property
 */
class PointProperty extends AbstractGeolocationProperty
{
    /**
     * Retrieve the property's type identifier.
     *
     * @return string
     */
    public function type()
    {
        return 'point-property';
    }

    /**
     * Retrieve the property geolocation format.
     *
     * @return string
     */
    public function geolocationType()
    {
        return 'point';
    }
}
