<?php

namespace Charcoal\Geolocation\Widget;

use UnexpectedValueException;
use InvalidArgumentException;
use ArrayAccess;

// from 'charcoal-property'
use Charcoal\Property\PropertyInterface;

// from 'charcoal-core'
use Charcoal\Model\ModelInterface;

// local dependencies
use Charcoal\Geolocation\Property\AbstractGeolocationProperty;

// from 'pimple'
use Pimple\Container;

// from 'charcoal-admin'
use Charcoal\Admin\AdminWidget;
use Charcoal\Admin\Support\HttpAwareTrait;

// from 'charcoal-ui'
use Charcoal\Admin\Ui\CollectionContainerInterface;
use Charcoal\Admin\Ui\CollectionContainerTrait;

/**
 * Displays object collection on a map.
 *
 * Geolocation Collection Widget
 */
class GeolocationCollectionWidget extends AdminWidget implements
    CollectionContainerInterface
{
    use CollectionContainerTrait;
    use HttpAwareTrait;

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
    private $geolocations;

    /**
     * @var string
     */
    private $geometryProperty;

    /**
     * @var PropertyInterface
     */
    private $geometryPropertyObject;

    /**
     * @var boolean
     */
    private $multiple;

    /**
     * @var string
     */
    private $geolocationType;

    /**
     * @var array|ModelInterface[]
     */
    private $geolocationObject;

    /**
     * @var string $infoboxTemplate
     */
    public $infoboxTemplate = '';

    /**
     * @param array $data The widget data.
     * @return self
     */
    public function setData(array $data)
    {
        parent::setData($data);

        $this->mergeDataSources($data);

        return $this;
    }

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
     * @return string
     */
    public function geometryProperty()
    {
        return $this->geometryProperty;
    }

    /**
     * @param string $p The property ident.
     * @return self
     */
    public function setGeometryProperty($p)
    {
        $this->geometryProperty = $p;

        return $this;
    }

    /**
     * @param string $template The infobox template ident.
     * @return self
     */
    public function setInfoboxTemplate($template)
    {
        $this->infoboxTemplate = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function infoboxTemplate()
    {
        return $this->infoboxTemplate;
    }

    /**
     * @throws InvalidArgumentException If the property is not an instance of AbstractGeolocationProperty.
     * @return PropertyInterface
     */
    public function geometryPropertyObject()
    {
        if (isset($this->geometryPropertyObject)) {
            return $this->geometryPropertyObject;
        }

        $this->geometryPropertyObject = $this->proto()->property($this->geometryProperty());

        if (!$this->geometryPropertyObject instanceof AbstractGeolocationProperty) {
            throw new InvalidArgumentException(sprintf(
                'Invalid Geometry property for %s, must be an instance of [%s]',
                $this->geometryProperty(),
                AbstractGeolocationProperty::class
            ));
        }

        return $this->geometryPropertyObject();
    }

    /**
     * @return boolean
     */
    public function multiple()
    {
        if (isset($this->multiple)) {
            return $this->multiple;
        }

        $this->multiple = $this->geometryPropertyObject()->multiple();

        return $this->multiple();
    }

    /**
     * @return string
     */
    public function geolocationType()
    {
        if (isset($this->geolocationType)) {
            return $this->geolocationType;
        }

        $this->multiple = $this->geometryPropertyObject()->geolocationType();
    }

    /**
     * Return all the objects with geographical information
     *
     * @throws UnexpectedValueException If the object type of the collection is missing.
     * @return array
     */
    public function geolocations()
    {
        if ($this->geolocations === null) {
            $objType = $this->objType();
            if (!$objType) {
                throw new UnexpectedValueException(sprintf(
                    '%1$s cannot create collection map. Object type is not defined.',
                    get_class($this)
                ));
            }

            $model = $this->proto();

            $loader = $this->collectionLoader();
            $loader->setModel($model);

            $collectionConfig = $this->collectionConfig();
            if (is_array($collectionConfig) && !empty($collectionConfig)) {
                unset($collectionConfig['properties']);
                $loader->setData($collectionConfig);
            }

            $geolocations = [];

            switch ($this->geolocationType()) {
                case 'point':
                default:
                    $callback = function (&$obj) use (&$geolocations) {
                        $geolocations = $this->parsePointCollection($obj);
                    };
                    break;
            }

            $loader->setCallback($callback->bindTo($this));
            $this->geolocationObject = $loader->load();

            $this->geolocations = $geolocations;
        }

        return $this->geolocations;
    }

    /**
     * @return \Generator
     */
    public function geolocationObjects()
    {
        if (!$this->geolocationObject) {
            $this->geolocations();
        }

        foreach ($this->geolocationObject as $obj) {
            $GLOBALS['widget_template'] = $this->infoboxTemplate();
            yield $obj;
            $GLOBALS['widget_template'] = '';
        }
    }

    /**
     * @return \Generator
     */
    public function infoboxes()
    {
        $geolocations         = $this->geolocations();
        $filteredGeolocations = [];

        array_walk($geolocations, function ($value) use (&$filteredGeolocations) {
            $id = $value['id'];

            if (!isset($filteredGeolocations[$id])) {
                $filteredGeolocations[$id] = $value;
            }
        });

        foreach ($filteredGeolocations as $geolocation) {
            $GLOBALS['widget_template'] = $this->infoboxTemplate();
            yield $geolocation;
            $GLOBALS['widget_template'] = '';
        }
    }

    /**
     *
     * @throws \Exception If the view instance is not previously set / injected.
     * @param ModelInterface|mixed $obj The object parsed from loader.
     * @return array
     */
    public function parsePointCollection(&$obj)
    {
        $out   = [];
        $value = $this->getPropertyValue($obj, $this->geometryProperty());
        $value = json_decode($value);

        if ($this->multiple()) {
            foreach ($value as $v) {
                $out[] = [
                    'type'     => 'marker',
                    'coords'   => $v,
                    'id'       => $obj->id(),
                    'edit_url' => $this->view()->renderTemplate(
                        '{{baseUrl}}admin/object/edit?'.
                        '{{#main_menu}}main_menu={{.}}&{{/main_menu}}obj_type={{obj_type}}&obj_id={{id}}',
                        $obj
                    )
                ];
            }
        } else {
            $out[] = [
                'type'     => 'marker',
                'coords'   => $value,
                'id'       => $obj->id(),
                'edit_url' => $this->view()->renderTemplate(
                    '{{baseUrl}}admin/object/edit?'.
                    '{{#main_menu}}main_menu={{.}}&{{/main_menu}}obj_type={{obj_type}}&obj_id={{id}}',
                    $obj
                )
            ];
        }

        return $out;
    }

    /**
     * @return boolean
     */
    public function showInfobox()
    {
        return ($this->infoboxTemplate != '');
    }

    /**
     * Fetch metadata from the current request.
     *
     * @return array
     */
    public function dataFromRequest()
    {
        return $this->httpRequest()->getParams($this->acceptedRequestData());
    }

    /**
     * Retrieve the accepted metadata from the current request.
     *
     * @return array
     */
    public function acceptedRequestData()
    {
        return [
            'obj_type',
            'obj_id',
            'collection_ident',
        ];
    }

    /**
     * Fetch metadata from the current object type.
     *
     * @return array
     */
    public function dataFromObject()
    {
        $proto         = $this->proto();
        $objMetadata   = $proto->metadata();
        $adminMetadata = (isset($objMetadata['admin']) ? $objMetadata['admin'] : null);

        if (empty($adminMetadata['lists'])) {
            return [];
        }

        $collectionIdent = $this->collectionIdent();
        if (!$collectionIdent) {
            $collectionIdent = $this->collectionIdentFallback();
        }

        if ($collectionIdent && $proto->view()) {
            $collectionIdent = $proto->render($collectionIdent);
        }

        if (!$collectionIdent) {
            return [];
        }

        if (isset($adminMetadata['lists'][$collectionIdent])) {
            $objListData = $adminMetadata['lists'][$collectionIdent];
        } else {
            $objListData = [];
        }

        $collectionConfig = [];

        if (isset($objListData['orders']) && isset($adminMetadata['list_orders'])) {
            $extraOrders = array_intersect(
                array_keys($adminMetadata['list_orders']),
                array_keys($objListData['orders'])
            );
            foreach ($extraOrders as $listIdent) {
                $collectionConfig['orders'][$listIdent] = array_replace_recursive(
                    $adminMetadata['list_orders'][$listIdent],
                    $objListData['orders'][$listIdent]
                );
            }
        }

        if (isset($objListData['filters']) && isset($adminMetadata['list_filters'])) {
            $extraFilters = array_intersect(
                array_keys($adminMetadata['list_filters']),
                array_keys($objListData['filters'])
            );
            foreach ($extraFilters as $listIdent) {
                $collectionConfig['filters'][$listIdent] = array_replace_recursive(
                    $adminMetadata['list_filters'][$listIdent],
                    $objListData['filters'][$listIdent]
                );
            }
        }

        if ($collectionConfig) {
            $this->mergeCollectionConfig($collectionConfig);
        }

        return $objListData;
    }

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        // Satisfies HttpAwareTrait dependencies
        $this->setHttpRequest($container['request']);

        $this->setCollectionLoader($container['model/collection/loader']);

        if (isset($container['admin/config']['apis.google.map.key'])) {
            $this->setApiKey($container['admin/config']['apis.google.map.key']);
        } elseif (isset($container['config']['apis.google.map.key'])) {
            $this->setApiKey($container['config']['apis.google.map.key']);
        }
    }

    /**
     * @param  ModelInterface $obj The object with the latitude property.
     * @param  string         $key The property to retrieve.
     * @throws InvalidArgumentException If the data key is missing.
     * @return mixed
     */
    protected function getPropertyValue(ModelInterface $obj, $key)
    {
        if (!is_string($key) || $key === '') {
            throw new InvalidArgumentException('Missing latitude property.');
        }

        if (isset($obj[$key])) {
            return $obj[$key];
        }

        $data     = null;
        $segments = explode('.', $key);
        if (count($segments) > 1) {
            $data = $obj;
            foreach (explode('.', $key) as $segment) {
                $accessible = is_array($data) || $data instanceof ArrayAccess;
                if ($data instanceof ArrayAccess) {
                    $exists = $data->offsetExists($segment);
                } else {
                    $exists = array_key_exists($segment, $data);
                }

                if ($accessible && $exists) {
                    $data = $data[$segment];
                } else {
                    return null;
                }
            }
        }

        return $data;
    }

    /**
     * Retrieve the default data source filters (when setting data on an entity).
     *
     * Note: Adapted from {@see \Slim\CallableResolver}.
     *
     * @link   https://github.com/slimphp/Slim/blob/3.x/Slim/CallableResolver.php
     * @param  mixed $toResolve A callable used when merging data.
     * @return callable|null
     */
    protected function resolveDataSourceFilter($toResolve)
    {
        if (is_string($toResolve)) {
            $model = $this->proto();

            $resolved = [$model, $toResolve];

            // check for slim callable as "class:method"
            $callablePattern = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';
            if (preg_match($callablePattern, $toResolve, $matches)) {
                $class  = $matches[1];
                $method = $matches[2];

                if ($class === 'parent') {
                    $resolved = [$model, $class.'::'.$method];
                }
            }

            $toResolve = $resolved;
        }

        return parent::resolveDataSourceFilter($toResolve);
    }

    // /**
    //  * @param mixed $rawPolygon The polygon information.
    //  * @return string
    //  */
    // private function formatPolygon($rawPolygon)
    // {
    //     if (is_string($rawPolygon)) {
    //         $polygon = explode(' ', $rawPolygon);
    //         $ret     = [];
    //         foreach ($polygon as $poly) {
    //             $coords = explode(',', $poly);
    //             if (count($coords) < 2) {
    //                 continue;
    //             }
    //             $ret[] = [(float)$coords[0], (float)$coords[1]];
    //         }
    //     } else {
    //         $ret = $rawPolygon;
    //     }
    //
    //     return json_encode($ret, true);
    // }

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
     * /**
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
     *
     * Retrieve the widget's data options for JavaScript components.
     *
     * @return array
     */
    public function widgetDataForJs()
    {
        return [
            'map_options' => $this->mapOptions(),
            'places'      => $this->geolocations()
        ];
    }
}
