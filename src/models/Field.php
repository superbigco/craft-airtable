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
class Field extends Model
{
    const TYPE_NUMBER      = 'number';
    const TYPE_EMAIL       = 'email';
    const TYPE_MULTISELECT = 'multiselect';
    const TYPE_DATETIME    = 'datetime';
    const TYPE_SELECT      = 'select';
    const TYPE_STRING      = 'string';
    const TYPE_DATE        = 'date';
    const TYPE_CHECKBOX    = 'checkbox';

    // Public Properties
    // =========================================================================

    public $id       = '';
    public $type     = 'string';
    public $required = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }
}
