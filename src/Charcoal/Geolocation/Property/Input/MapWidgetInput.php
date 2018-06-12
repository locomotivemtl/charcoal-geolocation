<?php

namespace Charcoal\Geolocation\Property\Input;

use InvalidArgumentException;

// From Pimple
use Pimple\Container;

// From 'charcoal-admin'
use Charcoal\Admin\Property\AbstractPropertyInput;

/**
 * Map Widget Input
 */
class MapWidgetInput extends AbstractPropertyInput
{
    const POINT = 'point';
    const POLYLINE = 'polyline';
    const POLYGON = 'polygon';

    /**
     * The API key for the mapping service.
     *
     * @var string
     */
    private $apiKey;

    /**
     * Settings for the map widget.
     *
     * @var array
     */
    private $mapOptions;

    /**
     * @var array
     */
    private $geolocationTypes = [
        self::POINT,
        self::POLYLINE,
        self::POLYGON
    ];

    /**
     * Sets the API key for the mapping service.
     *
     * @param  string $key An API key.
     * @return self
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;

        return $this;
    }

    /**
     * Retrieve API key for the mapping service.
     *
     * @return string
     */
    public function apiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set the map widget's options.
     *
     * This method always merges default settings.
     *
     * @param  array $settings The map widget options.
     * @return self
     */
    public function setMapOptions(array $settings)
    {
        if (isset($settings['api_key'])) {
            $this->setApiKey($settings['api_key']);
        }

        if ($this->mapOptions) {
            $this->mapOptions = array_replace_recursive($this->mapOptions, $settings);
        } else {
            $this->mapOptions = array_replace_recursive($this->defaultMapOptions(), $settings);
        }

        return $this;
    }

    /**
     * Merge (replacing or adding) map widget options.
     *
     * @param  array $settings The map widget options.
     * @return self
     */
    public function mergeMapOptions(array $settings)
    {
        if (isset($settings['api_key'])) {
            $this->setApiKey($settings['api_key']);
        }

        $this->mapOptions = array_merge($this->mapOptions, $settings);

        return $this;
    }

    /**
     * Add (or replace) an map widget option.
     *
     * @param  string $key The setting to add/replace.
     * @param  mixed  $val The setting's value to apply.
     * @throws InvalidArgumentException If the identifier is not a string.
     * @return self
     */
    public function addMapOption($key, $val)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(
                'Setting key must be a string.'
            );
        }

        // Make sure default options are loaded.
        if ($this->mapOptions === null) {
            $this->mapOptions();
        }

        $this->mapOptions[$key] = $val;

        if ($key === 'api_key') {
            $this->setApiKey($val);
        }

        return $this;
    }

    /**
     * Retrieve the map widget's options.
     *
     * @return array
     */
    public function mapOptions()
    {
        if ($this->mapOptions === null) {
            $this->mapOptions = $this->defaultMapOptions();
        }

        return $this->mapOptions;
    }

    /**
     * Retrieve the default map widget options.
     *
     * @return array
     */
    public function defaultMapOptions()
    {
        return ['api_key' => $this->apiKey()];
    }

    /**
     * Retrieve the map widget's options as a JSON string.
     *
     * @return string Returns data serialized with {@see json_encode()}.
     */
    public function mapOptionsAsJson()
    {
        return json_encode($this->mapOptions());
    }

    /**
     * @return array
     */
    private function allowedGeolocationTypes()
    {
        return [
            self::POINT,
            self::POLYLINE,
            self::POLYGON
        ];
    }

    /**
     * @return mixed
     */
    public function geolocationTypes()
    {
        return $this->geolocationTypes;
    }

    /**
     * @param mixed $type Geolocation type.
     * @throws InvalidArgumentException If type is not supported.
     * @return self
     */
    public function setGeolocationTypes($type)
    {
        if (is_array($type)) {
            array_walk($type, function ($value) {
                if (!in_array($value, $this->allowedGeolocationTypes())) {
                    throw new InvalidArgumentException(sprintf(
                        'Invalid Geolocation type provided for property [%s], received [%s]',
                        $this->inputName(),
                        $value
                    ));
                }
            });

            $this->geolocationTypes = $type;
        }

        if (is_string($type)) {
            if (!in_array($type, $this->allowedGeolocationTypes())) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid Geolocation type provided for property [%s], received [%s]',
                    $this->inputName(),
                    $type
                ));
            }

            $this->geolocationTypes = [$type];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function mapTools()
    {
        $types = array_values($this->geolocationTypes());

        return [
            self::POINT    => in_array(self::POINT, $types),
            self::POLYLINE => in_array(self::POLYLINE, $types),
            self::POLYGON  => in_array(self::POLYGON, $types)
        ];
    }

    /**
     * @return boolean
     */
    public function showMapTools()
    {
        return true;
    }

    /**
     * Retrieve the input name.
     *
     * The input name should always be the property's ident.
     *
     * @return string
     */
    public function inputName()
    {
        if ($this->inputName) {
            $name = $this->inputName;
        } else {
            $name = $this->propertyIdent();
        }

        if ($this->p()->l10n()) {
            $name .= '['.$this->lang().']';
        }

        return $name;
    }

    /**
     * Retrieve the control's data options for JavaScript components.
     *
     * @return array
     */
    public function controlDataForJs()
    {
        $data = [
            'map_options'        => $this->mapOptions(),
            'input_type'         => $this->inputType(),

            // Selectize Control
            'translations'       => [
                'point'    => $this->translator()->translate('point'),
                'polyline' => $this->translator()->translate('polyline'),
                'polygon'  => $this->translator()->translate('polygon'),
            ],

            // Base Property
            'required'           => $this->required(),
            'l10n'               => $this->property()->l10n(),
            'multiple'           => $this->multiple(),
            'multiple_separator' => $this->property()->multipleSeparator(),
            'multiple_options'   => $this->property()->multipleOptions(),
        ];

        return $data;
    }

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        if (isset($container['admin/config']['apis.google.map.key'])) {
            $this->setApiKey($container['admin/config']['apis.google.map.key']);
        } elseif (isset($container['config']['apis.google.map.key'])) {
            $this->setApiKey($container['config']['apis.google.map.key']);
        }
    }
}
