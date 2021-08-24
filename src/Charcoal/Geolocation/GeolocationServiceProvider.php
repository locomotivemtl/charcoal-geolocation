<?php

namespace Charcoal\Geolocation;

use Charcoal\Factory\FactoryInterface;
use Charcoal\Factory\GenericFactory;
use Charcoal\Geolocation\Model\GeometryConfigInterface;
use Charcoal\Geolocation\Model\LineStringGeometryConfig;
use Charcoal\Geolocation\Model\PointGeometryConfig;
use Charcoal\Geolocation\Model\PolygonGeometryConfig;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Service Provider: Geolocation
 */
class GeolocationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container Pimple DI container.
     * @return void
     */
    public function register(Container $container)
    {
        /**
         * Factory to create a geometry config classes.
         * Easily overridable to extend the configurability of geometries.
         *
         * @return FactoryInterface
         */
        $container['geolocation/geometry-config/factory'] = function (): FactoryInterface {
            return new GenericFactory([
                'map'           => [
                    'Point'      => PointGeometryConfig::class,
                    'LineString' => LineStringGeometryConfig::class,
                    'Polygon'    => PolygonGeometryConfig::class,
                ],
                'base_class'    => GeometryConfigInterface::class,
                'default_class' => PointGeometryConfig::class,
            ]);
        };
    }
}
