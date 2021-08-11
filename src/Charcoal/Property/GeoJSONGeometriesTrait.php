<?php

namespace Charcoal\Property;

use Charcoal\Property\Input\Geolocation\MapWidgetInput;
use Exception;
use InvalidArgumentException;

/**
 * Trait: GeoJSONGeometriesTrait
 * @package Charcoal\Property
 *
 *           A collection of GeoJSON compatible terminology as constants
 *          And a geometry prop
 */
trait GeoJSONGeometriesTrait
{
    /**
     * List of all allowed geometries.
     * This can be a list of geometries with their options :
     * [
     *      "multiPoint": {
     *          "limit": 5
     *      },
     *      "multiPolygon": {
     *          "limit": 4,
     *          "maxPoints": 15
     *      }
     * ]
     *
     * @var array
     */
    private $geometries;

    /**
     * @param string $type The geometry to test.
     * @return boolean
     */
    private function isAllowedGeometry(string $type): bool
    {
        return !!in_array($type, GeoJSONGeometriesInterface::GEOMETRY_LIST);
    }

    /**
     * @return array
     */
    public function getGeometries(): array
    {
        return $this->geometries ?: GeoJSONGeometriesInterface::GEOMETRY_LIST;
    }

    /**
     * @param array|null $types Geolocation type.
     * @return self
     * @throws InvalidArgumentException If type is not supported.
     */
    public function setGeometries(?array $types): self
    {
        if (!is_array($types)) {
            return $this;
        }

        // is Associative
        if ($types !== array_values($types)) {
            foreach ($types as $type => $data) {
                if (!$this->isAllowedGeometry($type)) {
                    throw new InvalidArgumentException(sprintf(
                        'Invalid Geolocation type provided for property [%s], received [%s]',
                        $this->inputName(),
                        $type
                    ));
                }

                $this->geometries[$type] = $data;
            }
        } else {
            // is Sequential
            foreach ($types as $type) {
                if (!$this->isAllowedGeometry($type)) {
                    throw new InvalidArgumentException(sprintf(
                        'Invalid Geolocation type provided for property [%s], received [%s]',
                        $this->inputName(),
                        $type
                    ));
                }

                $this->geometries[$type] = [];
            }
        }

        return $this;
    }
}
