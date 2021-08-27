<?php

namespace Charcoal\Admin\Geolocation;

use Exception;

/**
 * Class PolygonConverter
 */
class PolygonConverter extends AbstractGeometryConverter
{
    /**
     * @param mixed $data The data to convert from.
     * @return false|string|null
     * @throws Exception When the data is invalid.
     */
    public function convert($data)
    {
        $array = json_decode($data);

        if (!is_array($array)) {
            return null;
        }

        if ($this->getSwapXY()) {
            array_walk($array, function (&$value) {
                if (count($value) !== 2) {
                    throw new Exception('Invalid Polygon Data');
                }

                $value = array_reverse($value);
            });
        }

        // A valid GeoJSON polygon should always start and end with the same point.
        if (current($array) !== end($array)) {
            $array[] = $array[0];
        }

        /**
         * GeoJSON format {@see https://en.wikipedia.org/wiki/GeoJSON}
         */
        return json_encode([
            'type'        => 'Polygon',
            'coordinates' => [$array],
        ]);
    }
}
