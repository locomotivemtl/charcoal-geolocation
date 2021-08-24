<?php

namespace Charcoal\Geolocation\Model;

/**
 * Trait: GeometryConfigTrait
 * @package Charcoal\Geolocation\Model
 */
trait GeometryConfigTrait
{
    /**
     * @var boolean
     */
    protected $multiple;

    /**
     * @var null|int
     */
    protected $max;

    /**
     * @var boolean
     */
    protected $editable;

    /**
     * @var boolean
     */
    protected $draggable;

    /**
     * @return array
     */
    private function defaultData(): array
    {
        return [
            'multiple'  => false,
            'max'       => null,
            'editable'  => true,
            'draggable' => true,
        ];
    }

    /**
     * @return boolean
     */
    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @param boolean $multiple Multiple for PointGeometryConfig.
     * @return self
     */
    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @param integer|null $max Max for PointGeometryConfig.
     * @return self
     */
    public function setMax(?int $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEditable(): bool
    {
        return $this->editable;
    }

    /**
     * @param boolean $editable Editable for PointGeometryConfig.
     * @return self
     */
    public function setEditable(bool $editable): self
    {
        $this->editable = $editable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDraggable(): bool
    {
        return $this->draggable;
    }

    /**
     * @param boolean $draggable Movable for PointGeometryConfig.
     * @return self
     */
    public function setDraggable(bool $draggable): self
    {
        $this->draggable = $draggable;

        return $this;
    }
}
