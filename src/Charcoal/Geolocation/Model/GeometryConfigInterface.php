<?php

namespace Charcoal\Geolocation\Model;

use Charcoal\Config\EntityInterface;

/**
 * Interface: GeometryConfigInterface
 * @package Charcoal\Geolocation\Model
 */
interface GeometryConfigInterface extends EntityInterface
{
    /**
     * @return boolean
     */
    public function getMultiple(): bool;

    /**
     * @return integer|null
     */
    public function getMax(): ?int;

    /**
     * @return boolean
     */
    public function getEditable(): bool;

    /**
     * @return boolean
     */
    public function getDraggable(): bool;
}
