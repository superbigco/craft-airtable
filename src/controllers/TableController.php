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
use superbig\airtable\models\Form;
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
     * @return \yii\web\Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSave()
    {
        $form = Airtable::$plugin->getService()->filteredContent();

        // Validate
        if (!$form->validate()) {
            return $this->returnErrors($form);
        }

        if (!$form->hasErrors()) {
            Airtable::$plugin->getService()->saveOrUpdate($form);

            // Check for errors from the API
            if ($form->hasErrors()) {
                return $this->returnErrors($form);
            }
            else {
                return $this->returnSuccess($form);
            }
        }
    }

    /**
     * Returns a 'success' response.
     *
     * @param $model
     *
     * @return \yii\web\Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    private function returnSuccess(Form $model)
    {
        $return = [
        ];

        if (Craft::$app->getRequest()->getIsAjax()) {
            $return['success'] = true;

            //$return['id']      = $entry->id;
            return $this->asJson($return);
        }

        Craft::$app->getSession()->setNotice(Craft::t('airtable', 'Submission saved.'));

        return $this->redirectToPostedUrl($model->getData());
    }

    public function returnErrors(Form $model)
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
