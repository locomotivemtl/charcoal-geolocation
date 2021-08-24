<?php

namespace Charcoal\Property;

/**
 * Interface: GeoJSONGeometriesInterface
 * @package Charcoal\Property
 *
 *          A collection of GeoJSON compatible terminology as constants
 *          And a geometry prop
 */
interface GeoJSONGeometriesInterface
{
    // App Naming conventions
    public const POINT       = 'point';
    public const LINE_STRING = 'lineString';
    public const POLYGON     = 'polygon';

    // MySQL naming conventions
    public const MYSQL_POINT               = 'POINT';
    public const MYSQL_LINE_STRING         = 'LINESTRING';
    public const MYSQL_POLYGON             = 'POLYGON';
    public const MYSQL_GEOMETRY_COLLECTION = 'GEOMETRYCOLLECTION';

    /**
     * The complete list of valid geometries.
     *
     * @var array
     */
    public const GEOMETRY_LIST = [
        self::POINT,
        self::LINE_STRING,
        self::POLYGON,
    ];

    /**
     * The mySql geometry types mapping for simple geometries.
     *
     * @var array
     */
    public const MYSQL_GEOMETRY_MAP = [
        self::POINT       => self::MYSQL_POINT,
        self::LINE_STRING => self::MYSQL_LINE_STRING,
        self::POLYGON     => self::MYSQL_POLYGON,
    ];

    /**
     * @return array
     */
    public function getGeometries(): array;
}
