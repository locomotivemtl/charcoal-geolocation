<?php

namespace Charcoal\Geolocation\Property;

use PDO;
use InvalidArgumentException;

// from 'charcoal-property'
use Charcoal\Property\AbstractProperty;

// from 'charcoal-translation'
use Charcoal\Translator\Translation;

// local dependencies
use Charcoal\Geolocation\Property\GeolocationInterface;

/**
 * Class AbstractGeometryProperty
 */
abstract class AbstractGeolocationProperty extends AbstractProperty implements
    GeolocationInterface
{
    /**
     * The SQL data type.
     *
     * @var string
     */
    private $sqlType;

    /**
     * @return string
     */
    public function sqlExtra()
    {
        return '';
    }

    /**
     * Retrieve the property's SQL data type (storage format).
     *
     * For a lack of better array support in mysql, data is stored as encoded JSON in a TEXT.
     *
     * @return string
     */
    public function sqlType()
    {
        if ($this->sqlType === null) {
            $this->sqlType = 'TEXT';
        }

        return $this->sqlType;
    }

    /**
     * Retrieve the property's PDO data type.
     *
     * @return integer
     */
    public function sqlPdoType()
    {
        return PDO::PARAM_STR;
    }

    /**
     * @param   mixed $val     Optional. The value to to convert for input.
     * @param   array $options Optional input options.
     * @return  string
     */
    public function inputVal($val, array $options = [])
    {
        if ($val === null) {
            return '';
        }

        if (is_string($val)) {
            return $val;
        }

        /** Parse multilingual values */
        if ($this['l10n']) {
            $propertyValue = $this->l10nVal($val, $options);
            if ($propertyValue === null) {
                return '';
            }
        } elseif ($val instanceof Translation) {
            $propertyValue = (string)$val;
        } else {
            $propertyValue = $val;
        }

        return json_encode($propertyValue, JSON_PRETTY_PRINT);
    }

    /**
     * AbstractProperty > setVal(). Ensure val is an array
     *
     * @param  string|array $val The value to set.
     * @throws InvalidArgumentException If the value is invalid.
     * @return array
     */
    public function parseOne($val)
    {
        if ($val === null || $val === '') {
            if ($this['allowNull']) {
                return null;
            } else {
                throw new InvalidArgumentException(
                    'Value can not be NULL (not allowed)'
                );
            }
        }

        if (!is_array($val)) {
            $val = json_decode($val, true);
        }

        return $val;
    }

    /**
     * Get the property's value in a format suitable for storage.
     *
     * @param  mixed $val Optional. The value to convert to storage value.
     * @return mixed
     */
    public function storageVal($val)
    {
        if ($val === null || $val === '') {
            // Do not encode NULL values
            return null;
        }

        if (!$this['l10n'] && $val instanceof Translation) {
            $val = (string)$val;
        }

        if (is_array($val)) {
            $val = array_filter($val, function ($val) {
                return !!$val;
            });

            if (empty($val)) {
                return null;
            }
        }

        if (!is_scalar($val)) {
            return json_encode($val, JSON_UNESCAPED_UNICODE);
        }

        return $val;
    }
}
