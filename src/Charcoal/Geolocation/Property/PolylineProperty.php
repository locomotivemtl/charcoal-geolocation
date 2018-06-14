<?php

namespace Charcoal\Geolocation\Property;

/**
 * MapStructure Property
 */
class PolylineProperty extends AbstractGeolocationProperty
{
    /**
     * Retrieve the property's type identifier.
     *
     * @return string
     */
    public function type()
    {
        return 'polyline-property';
    }

    /**
     * Retrieve the property geolocation format.
     *
     * @return string
     */
    public function geolocationType()
    {
        return 'polyline';
    }
}
