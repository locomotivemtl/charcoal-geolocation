<?php

namespace Charcoal\Geolocation\Property;

// From 'charcoal-property'
use Charcoal\Property\StructureProperty;

/**
 * MapStructure Property
 */
class MapStructureProperty extends StructureProperty
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
}
