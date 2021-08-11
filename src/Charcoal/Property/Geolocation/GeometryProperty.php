<?php

namespace Charcoal\Property\Geolocation;

/**
 * Property: Geometry
 *
 * Property that stores all geometry types with native MySQL compatibility
 *
 * WKT format :
 *  GEOMETRYCOLLECTION(POINT(40 10), LINESTRING(10 10, 20 20, 10 40), POLYGON((40 40, 20 45, 45 30, 40 40)))
 *
 * GeoJSON format :
 * {@see GeometryCollection in https://en.wikipedia.org/wiki/GeoJSON}
 *
 */
class GeometryProperty extends AbstractGeometryProperty
{
    /**
     * @return string
     */
    public function type(): string
    {
        return 'geometry';
    }
}
