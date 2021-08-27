<?php

namespace Charcoal\Admin;

use Charcoal\Admin\Service\GeometryConverterService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Service Provider: Geolocation (admin)
 */
class GeolocationServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $container A container instance
     */
    public function register(Container $container)
    {
        $container['geolocation/geometry-converter'] = function (Container $container) {
            return new GeometryConverterService([
                'model/factory'           => $container['model/factory'],
                'model/collection/loader' => $container['model/collection/loader'],
            ]);
        };
    }
}
