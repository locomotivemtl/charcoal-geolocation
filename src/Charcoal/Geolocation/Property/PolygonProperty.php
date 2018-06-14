<?php

namespace Charcoal\Geolocation\Property;

/**
 * MapStructure Property
 */
class PolygonProperty extends AbstractGeolocationProperty
{
    /**
     * Retrieve the property's type identifier.
     *
     * @return string
     */
    public function type()
    {
        return 'polygon-property';
    }

    /**
     * Retrieve the property geolocation format.
     *
     * @return string
     */
    public function geolocationType()
    {
        return 'polygon';
    }
}
