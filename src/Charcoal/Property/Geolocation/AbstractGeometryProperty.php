<?php

namespace Charcoal\Property\Geolocation;

use Charcoal\Property\AbstractProperty;
use Charcoal\Property\GeoJSONGeometriesInterface;
use Charcoal\Property\GeoJSONGeometriesTrait;
use Charcoal\Translator\Translation;
use PDO;

/**
 * Abstract Geometry Property
 *
 * Property base to save Geometry data using the WKT format
 * {@see https://en.wikipedia.org/wiki/Well-known_text_representation_of_geometry}
 *
 * These property can save to mysql from the GeoJSON format and read WKT from database to GeoJSON
 * {@see https://en.wikipedia.org/wiki/GeoJSON}
 *
 * Conversions between both format is made in MySQL and functions to analyse spatial data is also available
 * directly from MySQL
 *
 * For more information on MySQL Spacial data and GeoJSON
 * {@see https://dev.mysql.com/doc/refman/5.7/en/spatial-geojson-functions.html#function_st-asgeojson}
 */
abstract class AbstractGeometryProperty extends AbstractProperty implements
    GeoJsonGeometriesInterface
{
    use GeoJSONGeometriesTrait;

    /**
     * @return string
     */
    public function sqlType(): string
    {
        if (!$this->getGeometries() || count($this->getGeometries()) > 1) {
            return GeoJsonGeometriesInterface::MYSQL_GEOMETRY_COLLECTION;
        }

        return GeoJsonGeometriesInterface::MYSQL_GEOMETRY_MAP[array_keys($this->getGeometries())[0]];
    }

    /**
     * @return integer
     */
    public function sqlPdoType(): int
    {
        return PDO::PARAM_STR;
    }

    /**
     * @param mixed $val     Optional. The value to to convert for input.
     * @param array $options Optional input options.
     * @return  string
     */
    public function inputVal($val, array $options = []): string
    {
        if ($val === null) {
            return '';
        }

        if (is_string($val)) {
            return $val;
        }

        /** Parse multilingual values */
        if ($this['l10n']) {
            $propertyValue = $this->l10nVal($val, $options);
            if ($propertyValue === null) {
                return '';
            }
        } elseif ($val instanceof Translation) {
            $propertyValue = (string)$val;
        } else {
            $propertyValue = $val;
        }

        return json_encode($propertyValue, JSON_PRETTY_PRINT);
    }

    /**
     * Get the property's value in a format suitable for storage.
     *
     * @param mixed $val Optional. The value to convert to storage value.
     * @return mixed
     */
    public function storageVal($val)
    {
        if ($val === null || $val === '') {
            // Do not encode NULL values
            return null;
        }

        if (!$this['l10n'] && $val instanceof Translation) {
            $val = (string)$val;
        }

        if (is_array($val)) {
            $val = array_filter($val, function ($val) {
                return !!$val;
            });

            if (empty($val)) {
                return null;
            }
        }

        if (is_scalar($val)) {
            $val = json_decode($val, true);
        }

        if (isset($val['features']) && count($val['features']) === 1) {
            $val = $val['features'][0]['geometry'];
        }

        return $val;
    }

    /**
     * Format the property's PDO binding parameter identifier.
     *
     * This method can be overridden for custom named placeholder parsing.
     *
     * This method allows a property to apply an SQL function to a named placeholder:
     *
     * ```sql
     * function ($param) {
     *     return 'ST_GeomFromGeoJSON('.$param.')';
     * }
     * ```
     *
     * This method returns a closure to be called during the processing of object
     * inserts and updates in {@see \Charcoal\Source\DatabaseSource}.
     *
     * @link https://www.php.net/manual/en/pdostatement.bindparam.php
     *
     * @return \Closure
     */
    protected function sqlPdoBindParamExpression(): \Closure
    {
        /**
         * @param string $bind The PDO bind string.
         */
        return function ($bind) {
            return 'ST_GeomFromGeoJSON('.$bind.')';
        };
    }

    /**
     * Overridable to allow for custom Field Name parsing.
     *
     * This allows to add additional functions to a select statement that looks like this :
     *  'MySQL_FUNCTION(select)'
     *
     * @return \Closure
     */
    protected function sqlSelectExpression(): \Closure
    {
        return function ($select) {
            return 'ST_AsGeoJSON('.$select.')';
        };
    }
}
