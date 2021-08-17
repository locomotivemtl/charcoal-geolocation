<?php

namespace Charcoal\Geolocation\Property;

/**
 * Provides geolocation data awareness.
 *
 * Geolocation Interface
 * @package Charcoal\Geolocation\Property
 */
interface GeolocationInterface
{
    /**
     * Retrieve the property geolocation format.
     *
     * @return string
     */
    public function geolocationType();
}
