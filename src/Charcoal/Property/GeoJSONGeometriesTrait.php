<?php

namespace Charcoal\Property;

use Charcoal\Factory\FactoryInterface;
use Charcoal\Geolocation\Model\GeometryConfigInterface;
use Exception;
use InvalidArgumentException;

/**
 * Trait: GeoJSONGeometriesTrait
 * @package Charcoal\Property
 *
 *           A geometry property compatible trait to handle geometries setter/getter
 *           Also parses the geometries' data as GeoJSON compatible data with support for options related to map input.
 *           Individual geometries are cast into geometry specific classes to streamline the map options attributions
 *           process.
 */
trait GeoJSONGeometriesTrait
{
    /**
     * List of all allowed geometries.
     * This can be a list of geometries with their options :
     * [
     *      "Point": {
     *          "max": 5
     *      },
     *      "Polygon": {
     *          "max": 4,
     *          "allowExtrusion": true
     *      }
     * ]
     *
     * @var array
     */
    private $geometries;

    /**
     * @var FactoryInterface $geometryConfigFactory
     */
    private $geometryConfigFactory;

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
        if (!$this->geometries) {
            $this->setGeometries(GeoJSONGeometriesInterface::GEOMETRY_LIST);
        }

        return $this->geometries;
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
                        $this->ident(),
                        $type
                    ));
                }

                if (!$data instanceof GeometryConfigInterface) {
                    $data = $this->getGeometryConfigFactory()->create($type)->setData($data);
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

                $this->geometries[$type] = $this->getGeometryConfigFactory()->create($type);
            }
        }

        return $this;
    }

    /**
     * @return FactoryInterface
     * @throws Exception When a dependency is missing.
     */
    public function getGeometryConfigFactory(): FactoryInterface
    {
        if (!$this->geometryConfigFactory instanceof FactoryInterface) {
            throw new Exception('Missing geometry config factory dependency');
        }

        return $this->geometryConfigFactory;
    }

    /**
     * @param FactoryInterface $geometryConfigFactory GeometryConfigFactory for GeoJSONGeometriesTrait.
     * @return self
     */
    public function setGeometryConfigFactory(FactoryInterface $geometryConfigFactory): self
    {
        $this->geometryConfigFactory = $geometryConfigFactory;

        return $this;
    }
}
