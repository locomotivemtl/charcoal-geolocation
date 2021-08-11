<?php

namespace Charcoal\Property\Input\Geolocation;

use Charcoal\Admin\Property\AbstractPropertyInput;
use Charcoal\Property\GeoJSONGeometriesInterface;
use Charcoal\Property\GeoJSONGeometriesTrait;
use InvalidArgumentException;
use Pimple\Container;

/**
 * Map Widget Input
 */
class MapWidgetInput extends AbstractPropertyInput implements
    GeoJSONGeometriesInterface
{
    use GeoJSONGeometriesTrait;

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
     * Sets the API key for the mapping service.
     *
     * @param string $key An API key.
     * @return self
     */
    public function setApiKey(string $key): self
    {
        $this->apiKey = $key;

        return $this;
    }

    /**
     * Retrieve API key for the mapping service.
     *
     * @return string
     */
    public function apiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Set the map widget's options.
     *
     * This method always merges default settings.
     *
     * @param array $settings The map widget options.
     * @return self
     */
    public function setMapOptions(array $settings): self
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
     * @param array $settings The map widget options.
     * @return self
     */
    public function mergeMapOptions(array $settings): self
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
     * @param string $key The setting to add/replace.
     * @param mixed  $val The setting's value to apply.
     * @return self
     * @throws InvalidArgumentException If the identifier is not a string.
     */
    public function addMapOption(string $key, $val): self
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
     * Retrieve the default map widget options.
     *
     * @return array
     */
    public function defaultMapOptions(): array
    {
        $options = [
            'api_key'  => $this->apiKey(),
            'multiple' => true,
        ];

        if (isset($this->mapConfig)) {
            $config = $this->mapConfig;

            if ($config['map'] && $config['map']['defaultCenter']) {
                $options['map'] = [
                    'center' => [
                        'x' => $config['map']['defaultCenter'][0],
                        'y' => $config['map']['defaultCenter'][1],
                    ],
                ];
            }
        }

        return $options;
    }

    /**
     * Retrieve the map widget's options.
     *
     * @return array
     */
    public function mapOptions(): array
    {
        if ($this->mapOptions === null) {
            $this->mapOptions = $this->defaultMapOptions();
        }

        return $this->mapOptions;
    }

    /**
     * Retrieve the map widget's options as a JSON string.
     *
     * @return string Returns data serialized with {@see json_encode()}.
     */
    public function mapOptionsAsJson(): string
    {
        return json_encode($this->mapOptions());
    }

    /**
     * @return array
     */
    public function mapTools(): array
    {
        return $this->getGeometries();
    }

    /**
     * @return boolean
     */
    public function showMapTools(): bool
    {
        return true;
    }

    /**
     * Retrieve the control's data options for JavaScript components.
     *
     * @return array
     */
    public function controlDataForJs(): array
    {
        return [
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
            'l10n'               => $this->property()['l10n'],
            'multiple'           => $this->multiple(),
            'multiple_separator' => $this->property()->multipleSeparator(),
            'multiple_options'   => $this->property()['multipleOptions'],
        ];
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

        if (isset($container['map/config'])) {
            $this->mapConfig = $container['map/config'];
        }
    }

    // /**
    //  * @return string
    //  * @throws UnexpectedValueException If the value is invalid.
    //  * @uses   AbstractProperty::inputVal() Must handle string sanitization of value.
    //  */
    // public function inputVal()
    // {
    //     $val = parent::inputVal();
    //
    //     $val = json_decode($val, true);
    //     if (is_array($val)) {
    //         $val = array_reduce($val, function ($carry = '', $value = []) {
    //             return ltrim(implode(' ', [$carry, implode(',', $value)]), ' ');
    //         });
    //     }
    //
    //     error_log(var_export($val, true));
    //
    //     return json_encode($val);
    // }
}
