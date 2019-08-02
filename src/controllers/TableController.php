<?php
/**
 * Airtable plugin for Craft CMS 3.x
 *
 * Sweet saving and fetching of data with Airtable
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\airtable\controllers;

use superbig\airtable\Airtable;
use superbig\subscriptionemails\SubscriptionEmails;

use Craft;
use craft\web\Controller;

/**
 * @author    Superbig
 * @package   Airtable
 * @since     1.0.0
 */
class TableController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     */
    public function actionSave()
    {
        $content = Airtable::$plugin->getService()->filteredContent();

        // Validate
        if (!$content->validate()) {
            $this->returnErrors($content);
        }

        if (!$content->hasErrors()) {
            $response = Airtable::$plugin->getService()->saveOrUpdate($content);

            // Check for errors from the API
            if ($content->hasErrors()) {
                $this->returnErrors($content);
            }
            else {
                $this->returnSuccess($content);
            }

        }
    }

    /**
     * Returns a 'success' response.
     *
     * @param $entry
     *
     * @return void
     */
    private function returnSuccess($model)
    {
        //$successEvent = new GuestEntriesEvent($this, array( 'entry' => $entry, 'faked' => $faked ));

        if (Craft::$app->getRequest()->getIsAjax()) {
            $return['success'] = true;

            //$return['id']      = $entry->id;
            return $this->asJson($return);
        }
        else {
            Craft::$app->getSession()->setNotice(Craft::t('airtable', 'Submission saved.'));

            return $this->redirectToPostedUrl($model);
        }
    }

    public function returnErrors($model)
    {
        //$errorEvent = new GuestEntriesEvent($this, array( 'entry' => $entry ));
        //craft()->guestEntries->onError($errorEvent);

        if (Craft::$app->getRequest()->getIsAjax()) {
            return $this->asJson([
                'airtable' => $model->getErrors(),
            ]);
        }
        else {
            Craft::$app->getSession()->setError(Craft::t('airtable', 'Couldn’t save record.'));

            // Send the airtable input back to the template
            //$entryVariable = craft()->config->get('entryVariable', 'guestentries');
            Craft::$app->getUrlManager()->setRouteParams([
                'airtable' => $model,
            ]);
        }
    }
}
