<?php
/**
 * Airtable plugin for Craft CMS 3.x
 *
 * Sweet saving and fetching of data with Airtable
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\airtable;


use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;

use craft\web\twig\variables\CraftVariable;
use superbig\airtable\models\Settings;
use superbig\airtable\services\AirtableService;
use superbig\airtable\variables\AirtableVariable;
use yii\base\Event;

/**
 * Class Airtable
 *
 * @author    Superbig
 * @package   Airtable
 * @since     1.0.0
 *
 * @property AirtableService $service
 * @method Settings getSettings()
 */
class Airtable extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Airtable
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'service' => AirtableService::class,
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('airtable', AirtableVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'airtable',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * @return AirtableService
     * @throws \yii\base\InvalidConfigException
     */
    public function getService(): AirtableService
    {
        /** @var AirtableService $var */
        $module = $this->get('service');

        return $module;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            'airtable/settings',
            [
                'settings' => $this->getSettings(),
            ]
        );
    }
}
