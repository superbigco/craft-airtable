<?php
/**
 * Airtable plugin for Craft CMS 3.x
 *
 * Sweet saving and fetching of data with Airtable
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\airtable\models;

use craft\helpers\DateTimeHelper;
use superbig\subscriptionemails\SubscriptionEmails;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   Airtable
 * @since     1.0.0
 *
 * @property Field[] $fields
 */
class Records
{
    // Public Properties
    // =========================================================================

    public $table  = '';
    public $fields = [];
    public $data   = [];
    public $server;

    // Public Methods
    // =========================================================================

    public static function fromRecords(array $records = [], $table = null, $base = null)
    {
        $model        = new self();
        $model->data  = \array_map(function($row) use ($base, $table) {
            return Record::fromRecord($row, $table, $base);
        }, $records);
        $model->table = $table;

        return $model;
    }

    public function addField(Field $field)
    {
        $this->fields[ $field->id ] = $field;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return Field[]
     */
    public function getDateFields()
    {
        $fields = $this->getFields();

        return array_filter($fields, function(Field $field) {
            return $field->type === Field::TYPE_DATE;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function setField($key = '', $value = '')
    {
        $this->data[ $key ] = $value;

        $this->formatValue($key);

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setTable(string $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return array
     */
    protected function defineAttributes()
    {
        $definedFields = [];

        foreach ($this->getFields() as $index => $fieldKey) {
            if (is_array($fieldKey)) {
                $config   = $fieldKey;
                $type     = 'string';
                $default  = '';
                $required = !empty($config['required']);

                if (isset($config['type'])) {
                    $type = $config['type'];
                }

                switch ($type) {
                    case 'number':
                        $typeClass = AttributeType::Number;
                        break;
                    case 'email':
                        $typeClass = AttributeType::Email;
                        break;
                    case 'checkbox':
                        $typeClass = AttributeType::Bool;
                        break;
                    case 'multiselect':
                        $typeClass = AttributeType::Mixed;
                        break;
                    case 'date':
                        $typeClass = AttributeType::DateTime;
                        break;
                    case 'datetime':
                        $typeClass = AttributeType::DateTime;
                        break;
                    case 'select':
                    default:
                        $typeClass = AttributeType::String;
                }

                if ($type === 'number') {
                    $default = null;
                }

                if ($type === 'checkbox') {
                    $default = false;
                }

                if ($type === 'multiselect') {
                    $default = [];
                }

                $definedFields[ $config['id'] ] = [$typeClass, 'default' => $default, 'required' => $required];
            }
            else {
                // Default to String, required
                $definedFields[ $fieldKey ] = [AttributeType::String, 'default' => '', 'required' => true];
            }
        }

        return $definedFields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    private function formatValue(string $key)
    {
        $field = $this->fields[ $key ];
        $value = $this->data[ $key ];

        if ($field->type === Field::TYPE_DATE || $field->type === Field::TYPE_DATETIME) {
            $value = DateTimeHelper::toDateTime($value);
        }

        if ($field->type === Field::TYPE_CHECKBOX) {
            $value = !empty($value);
        }

        if ($field->type === Field::TYPE_NUMBER) {
            $value = (float)$value;
        }

        $this->data[ $key ] = $value;
    }
}
