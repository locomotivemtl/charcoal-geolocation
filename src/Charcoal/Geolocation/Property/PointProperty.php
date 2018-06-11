<?php

namespace Charcoal\Geolocation\Property;

// From 'charcoal-property'
use Charcoal\Property\StructureProperty;

/**
 * MapStructure Property
 */
class PointProperty extends StructureProperty
{
    /**
     * Retrieve the property's type identifier.
     *
     * @return string
     */
    public function type()
    {
        return 'charcoal/geolocation/property/point-property';
    }
}
