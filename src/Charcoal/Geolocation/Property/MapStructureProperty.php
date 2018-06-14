<?php

namespace Charcoal\Geolocation\Property;

// From 'charcoal-property'
use Charcoal\Property\StructureProperty;

// local dependencies
use Charcoal\Geolocation\Property\GeolocationInterface;

/**
 * MapStructure Property
 */
class MapStructureProperty extends StructureProperty implements
    GeolocationInterface
{
    /**
     * Retrieve the property's type identifier.
     *
     * @return string
     */
    public function type()
    {
        return 'charcoal/geolocation/property/map-structure-property';
    }

    /**
     * Retrieve the property geolocation format.
     *
     * @return string
     */
    public function geolocationType()
    {
        return 'structure';
    }

    /**
     * @param boolean $multiple Is the input multiple.
     * @throws \RuntimeException When multiple is set to TRUE.
     * @return \Charcoal\Property\AbstractProperty
     */
    public function setMultiple($multiple)
    {
        if ($multiple) {
            throw new \RuntimeException(sprintf(
                '[%s] Cannot be multiple',
                __CLASS__
            ));
        }

        return parent::setMultiple($multiple);
    }
}
