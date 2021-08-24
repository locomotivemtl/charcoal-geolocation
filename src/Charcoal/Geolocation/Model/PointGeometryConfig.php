<?php

namespace Charcoal\Geolocation\Model;

use Charcoal\Config\AbstractEntity;

/**
 * Entity: PointGeometryConfig
 */
class PointGeometryConfig extends AbstractEntity implements
    GeometryConfigInterface
{
    use GeometryConfigTrait;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->setData($this->defaultData());
    }
}
