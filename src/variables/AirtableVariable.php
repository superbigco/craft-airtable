<?php
/**
 * Airtable plugin for Craft CMS 3.x
 *
 * Sweet saving and fetching of data with Airtable
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\airtable\variables;

use superbig\airtable\Airtable;
use superbig\subscriptionemails\SubscriptionEmails;

use Craft;

/**
 * @author    Superbig
 * @package   Airtable
 * @since     1.0.0
 */
class AirtableVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @return \superbig\airtable\services\AirtableService
     * @throws \yii\base\InvalidConfigException
     */
    public function records()
    {
        return Airtable::$plugin->getService();
    }

    public function find($criteria = [])
    {
        return Airtable::$plugin->getService()->find($criteria);
    }
}
