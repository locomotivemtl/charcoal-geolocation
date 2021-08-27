<?php

namespace Charcoal\Admin\Geolocation;

/**
 * Abstract Geometry Converter
 */
abstract class AbstractGeometryConverter
{
    /**
     * @var bool
     */
    protected $swapXY;

    /**
     * @param array $data Init data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['swapXY'])) {
            $this->setSwapXY($data['swapXY']);
        }
    }

    /**
     * @param mixed $data The data to convert from.
     * @return mixed
     */
    abstract public function convert($data);

    /**
     * @return boolean
     */
    public function getSwapXY(): bool
    {
        return $this->swapXY;
    }

    /**
     * @param boolean $swapXY SwapXY for AbstractGeometryConverter.
     * @return self
     */
    public function setSwapXY(bool $swapXY): self
    {
        $this->swapXY = $swapXY;

        return $this;
    }
}
