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
 *
 * @property string $defaultTable
 * @property string $apiKey
 * @property string $base
 * @property array  $allowedFields Allowed field keys. This matches the field names in the table. See the documentation for a complete example of how to set column type and validation. If not defined, all fields defaults to string, and is required
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $allowedTables = [];
    public $allowedFields = [];
    public $apiKey;
    public $base;
    public $defaultTable;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apiKey', 'base'], 'string'],
            [['apiKey', 'base'], 'required'],
        ];
    }
}
