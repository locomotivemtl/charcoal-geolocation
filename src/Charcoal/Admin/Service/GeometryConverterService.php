<?php

namespace Charcoal\Admin\Service;

use Charcoal\Admin\Geolocation\PolygonConverter;
use Charcoal\Config\AbstractEntity;
use Charcoal\Loader\CollectionLoaderAwareTrait;
use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Model\ModelInterface;
use Charcoal\Property\PropertyField;
use Charcoal\Source\DatabaseSource;
use Exception;
use InvalidArgumentException;

/**
 * Service: Geometry Conversion
 *
 * Handle the conversion from legacy spatial notation to the Native MySql geometry notation.
 */
class GeometryConverterService extends AbstractEntity
{
    use ModelFactoryTrait;
    use CollectionLoaderAwareTrait;

    private $converter;
    private $objType       = 'city/parking/object/zone';
    private $propertyIdent = 'geometry';
    private $backupPropertyIdentifier;

    private $property;
    private $proto;

    /**
     * @param array $data Init data.
     */
    public function __construct(array $data)
    {
        $this->setModelFactory($data['model/factory']);
        $this->setCollectionLoader($data['model/collection/loader']);
    }

    /**
     * Prepare the needed service resources.
     *
     * @return void
     */
    private function prepare()
    {
        // Prepare proto
        $proto = $this->modelFactory()->create($this->getObjType());
        $this->setProto($proto);

        // Prepare property
        $property = $this->getProto()->property($this->getPropertyIdent());
        $this->setProperty($property);
    }

    /**
     * Create a backup column in the database to save the current data.
     *
     * @return void
     * @throws Exception When the source property is not found in the database structure.
     */
    public function createBackupColumn()
    {
        /** @var DatabaseSource $source */
        $source = $this->getProto()->source();

        $propIdent = $this->getPropertyIdent();
        $structure = $source->tableStructure();

        if (!isset($structure[$propIdent])) {
            throw new Exception('No database structure found for '.$propIdent.' in '.__CLASS__);
        }

        $columnData = $structure[$propIdent];

        $backupIdent = $this->getBackupPropertyIdentifier();

        // Don't alter table if column name already exists.
        if (isset($structure[$backupIdent])) {
            return;
        }

        $fieldData = [
            'ident'      => $backupIdent,
            'sqlType'    => $columnData['Type'],
            'allowNull'  => $columnData['Null'] === 'YES',
            'defaultVal' => $columnData['Default'],
            'extra'      => $columnData['Extra'],
        ];

        $field = new PropertyField();
        $field->setData($fieldData);

        $sql = strtr(
            'ALTER TABLE `%table` ADD COLUMN %field AFTER %targetColumn',
            [
                '%table'        => $source->table(),
                '%field'        => $field->sql(),
                '%targetColumn' => $propIdent,
            ]
        );

        $source->db()->query($sql);
    }

    /**
     * @return false|\PDOStatement
     */
    private function backupGeometry()
    {
        /** @var DatabaseSource $source */
        $source = $this->getProto()->source();

        $sql = strtr(
            'UPDATE `%table` SET `%backupCol` = `%targetCol`',
            [
                '%table'     => $source->table(),
                '%backupCol' => $this->getBackupPropertyIdentifier(),
                '%targetCol' => $this->getPropertyIdent(),
            ]
        );

        return $source->db()->query($sql);
    }

    /**
     * @return false|\PDOStatement
     */
    private function emptyTargetColumn()
    {
        /** @var DatabaseSource $source */
        $source = $this->getProto()->source();

        $sql = strtr(
            'UPDATE `%table` SET `%targetCol` = null',
            [
                '%table'     => $source->table(),
                '%targetCol' => $this->getPropertyIdent(),
            ]
        );

        return $source->db()->query($sql);
    }

    private function processConversion()
    {
        /** @var DatabaseSource $source */
        $source = $this->getProto()->source();

        $sql = strtr(
            'SELECT * FROM %table WHERE %targetCol IS NULL and %backupCol IS NOT NULL',
            [
                '%table'     => $source->table(),
                '%targetCol' => $this->getPropertyIdent(),
                '%backupCol' => $this->getBackupPropertyIdentifier(),
            ]
        );

        $loader = $this->collectionLoader()->setModel($this->getProto());

        foreach ($loader->loadFromQuery($sql) as $item) {
            // Here we have to convert geo-data using different converters.
            $converter = new PolygonConverter(['swapXY' => true]);
            $convertedData = $converter->convert($item[$this->getBackupPropertyIdentifier()]);
            $item[$this->getPropertyIdent()] = $convertedData;
            $item->update();
        };
    }

    /**
     * @param array $options
     * @return void
     * @throws Exception
     */
    public function convert(array $options = [])
    {
        if ($options) {
            $this->setData($options);
        }

        $this->prepare();

        // Create backup column
        $this->createBackupColumn();

        // Backup old data
        if ($this->backupGeometry() === false) {
            throw new Exception('Was not able to backup geometry for conversion');
        };

        // Empty target column
        $this->emptyTargetColumn();

        // Alter Table to conform to new property metadata
        $this->getProto()->source()->alterTable();

        // Transform
        $this->processConversion();
    }

    // Getters / Setters
    // ==========================================================================

    /**
     * @return mixed
     * @throws InvalidArgumentException When the objType is missing.
     */
    public function getObjType()
    {
        if (!$this->objType) {
            throw new InvalidArgumentException('The object type is missing for '.__CLASS__);
        }

        return $this->objType;
    }

    /**
     * @param mixed $objType ObjType for GeometryConversionService.
     * @return self
     */
    public function setObjType($objType): self
    {
        $this->objType = $objType;

        return $this;
    }

    /**
     * @return ModelInterface
     * @throws InvalidArgumentException When the proto is missing.
     */
    public function getProto()
    {
        if (!$this->proto) {
            throw new InvalidArgumentException('The prototype is missing for '.__CLASS__);
        }

        return $this->proto;
    }

    /**
     * @param ModelInterface $proto Proto for GeometryConversionService.
     * @return self
     */
    public function setProto(ModelInterface $proto): self
    {
        $this->proto = $proto;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPropertyIdent()
    {
        return $this->propertyIdent;
    }

    /**
     * @param mixed $propertyIdent PropertyIdent for GeometryConversionService.
     * @return self
     */
    public function setPropertyIdent($propertyIdent): self
    {
        $this->propertyIdent = $propertyIdent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBackupPropertyIdentifier()
    {
        return ($this->backupPropertyIdentifier ?? $this->getPropertyIdent().'_backup');
    }

    /**
     * @param mixed $backupPropertyIdentifier BackupPropertyIdentifier for GeometryConversionService.
     * @return self
     */
    public function setBackupPropertyIdentifier($backupPropertyIdentifier): self
    {
        $this->backupPropertyIdentifier = $backupPropertyIdentifier;

        return $this;
    }

    /**
     * @return mixed
     * @throws InvalidArgumentException When the property is missing.
     */
    public function getProperty()
    {
        if (!$this->property) {
            throw new InvalidArgumentException('The property is missing for '.__CLASS__);
        }

        return $this->property;
    }

    /**
     * @param mixed $property Property for GeometryConversionService.
     * @return self
     */
    public function setProperty($property): self
    {
        $this->property = $property;

        return $this;
    }
}
