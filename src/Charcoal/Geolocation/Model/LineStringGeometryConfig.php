<?php

namespace Charcoal\Geolocation\Model;

use Charcoal\Config\AbstractEntity;

/**
 * Entity: LineStringGeometryConfig
 */
class LineStringGeometryConfig extends AbstractEntity implements
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
