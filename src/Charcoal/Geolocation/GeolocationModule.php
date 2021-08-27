<?php

namespace Charcoal\Geolocation;

use Charcoal\App\Module\AbstractModule;
use Pimple\Container;

/**
 * Class GeolocationModule
 */
class GeolocationModule extends AbstractModule
{
    public const ADMIN_CONFIG = 'vendor/locomotivemtl/charcoal-geolocation/config/admin.json';

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

        // Hack: only if the request start with '/admin'
        if ($this->isPathAdmin($container['request']->getUri()->getPath())) {
            $adminGeolocationServiceProvider = new \Charcoal\Admin\GeolocationServiceProvider();
            $container->register($adminGeolocationServiceProvider);
        }

        return $this;
    }

    /**
     * @param string $path The path to check.
     * @return boolean
     */
    private function isPathAdmin(string $path): bool
    {
        $path = ltrim($path, '/');
        if ($path === 'admin') {
            return true;
        }

        if (substr($path, 0, 6) === 'admin/') {
            return true;
        }

        return false;
    }
}
