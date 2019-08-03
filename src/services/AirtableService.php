<?php
/**
 * Airtable plugin for Craft CMS 3.x
 *
 * Sweet saving and fetching of data with Airtable
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\airtable\services;

use DateTime;
use superbig\airtable\Airtable;
use superbig\airtable\models\Field;
use superbig\airtable\models\Form;

use Craft;
use craft\base\Component;

/**
 * @author    Superbig
 * @package   Airtable
 * @since     1.0.0
 *
 * @property \Armetiz\AirtableSDK\Airtable $client
 */
class AirtableService extends Component
{
    // Public Methods
    // =========================================================================

    protected $client;
    protected $defaultTable;
    protected $table;
    protected $allowedFields;
    protected $base;
    protected $apiKey;
    protected $currentBase;

    public function init()
    {
        parent::init();

        $settings            = Airtable::$plugin->getSettings();
        $this->apiKey        = $settings->apiKey;
        $this->base          = $settings->base;
        $this->defaultTable  = $settings->defaultTable;
        $this->table         = $this->defaultTable;
        $this->allowedFields = $settings->allowedFields;
    }

    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    public function base($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * @return Form
     */
    public function filteredContent()
    {
        $form = new Form([
            'table' => $this->table,
        ]);

        // Get fields
        $fields  = $this->getFields();
        $request = Craft::$app->getRequest();

        $unverifiedTableParam = $request->getParam('table');
        if ($table = Craft::$app->getSecurity()->validateData($unverifiedTableParam)) {
            $form->setTable($table);
        }

        foreach ($fields as $id) {
            if (\is_string($id)) {
                $field = new Field([
                    'id' => $id,
                ]);
            }
            else {
                $config = $id;
                $field  = new Field([
                    'id'   => $config['id'],
                    'type' => $config['type'] ?? 'string',
                ]);
            }

            $value = $request->getParam($field->id);

            $form->addField($field);
            $form->setField($field->id, $value);
        }

        return $form;
    }

    public function find($criteria = [])
    {
        try {
            $records = $this->getClient()->findRecords($this->table, $criteria);

            return $records;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function saveOrUpdate(Form $form, $id = null)
    {
        $criteria = [];
        $data     = $form->getData();

        // Process dates
        foreach ($form->getDateFields() as $field) {
            $key   = $field->id;
            $value = $data[ $key ] ?? null;

            if ($value && $value instanceof DateTime) {
                $data[ $key ] = $value->format(DateTime::ISO8601);
            }
        }

        if (!empty($id)) {
            $criteria['Id'] = $id;
        }

        try {
            if (isset($criteria['Id']) && $this->getClient()->containsRecord($this->table, $criteria)) {
                $this->getClient()->updateRecord($this->table, $criteria, $data);
            }
            else {
                $this->getClient()->createRecord($this->table, $data);
            }
        } catch (\Exception $e) {
            $form->addError('server', $e->getMessage());
        }
    }

    public function clear()
    {
        return $this->getClient()->flushRecords($this->table);
    }

    public function delete($id)
    {
        return $this->getClient()->deleteRecord($this->table, ["Id" => $id]);
    }

    public function getClient()
    {
        if ($this->base !== $this->currentBase) {
            $this->currentBase = $this->base;

            $this->client = new \Armetiz\AirtableSDK\Airtable($this->apiKey, $this->currentBase);
        }

        return $this->client;
    }

    public function getFields()
    {
        $fields = $this->allowedFields;

        return $fields;
    }
}
