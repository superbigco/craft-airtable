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

use Armetiz\AirtableSDK\Record as AirtableRecord;
use craft\helpers\DateTimeHelper;
use superbig\subscriptionemails\SubscriptionEmails;

use Craft;
use craft\base\Model;
use yii\base\InvalidCallException;
use yii\base\UnknownPropertyException;

/**
 * @author    Superbig
 * @package   Airtable
 * @since     1.0.0
 *
 * @property Field[] $fields
 */
class Record implements \ArrayAccess
{
    // Public Properties
    // =========================================================================

    public $id;
    public $table  = '';
    public $fields = [];
    public $data   = [];
    public $server;

    // Public Methods
    // =========================================================================

    public static function fromRecord(AirtableRecord $record, $table = null, $base = null)
    {
        $model        = new self();
        $model->id    = $record->getId();
        $model->data  = $record->getFields();
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

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        }
        else {
            $this->data[ $offset ] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[ $offset ]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[ $offset ]);
    }

    public function offsetGet($offset)
    {
        return isset($this->data[ $offset ]) ? $this->data[ $offset ] : null;
    }
}
