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

use superbig\subscriptionemails\SubscriptionEmails;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   Airtable
 * @since     1.0.0
 */
class Form extends Model
{
    // Public Properties
    // =========================================================================

    public $table  = '';
    public $fields = [];
    public $data   = [];

    // Public Methods
    // =========================================================================

    public function addField(Field $field)
    {
        $this->fields[ $field->id ] = $field;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setField($key = '', $value = '')
    {
        $this->data[ $key ] = $value;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }
}
