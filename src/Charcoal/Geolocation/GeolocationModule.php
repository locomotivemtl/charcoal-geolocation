<?php

namespace Charcoal\Geolocation;

use Charcoal\App\Module\AbstractModule;
use Pimple\Container;

/**
 * Class GeolocationModule
 */
class GeolocationModule extends AbstractModule
{
    /**
     * Setup the module's dependencies.
     *
     * @return self
     */
    public function setup(): self
    {
        /** @var Container $container */
        $container = $this->app()->getContainer();

        $geolocationServiceProvider = new GeolocationServiceProvider();
        $container->register($geolocationServiceProvider);

        return $this;
    }
}
