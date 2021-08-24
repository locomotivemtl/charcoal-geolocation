<?php

namespace Charcoal\Geolocation\Model;

use Charcoal\Config\AbstractEntity;

/**
 * Entity: PolygonGeometryConfig
 */
class PolygonGeometryConfig extends AbstractEntity implements
    GeometryConfigInterface
{
    use GeometryConfigTrait;

    protected $allowExtrusions = false;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->setData($this->defaultData());
    }
}
