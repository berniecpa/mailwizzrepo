<?php declare(strict_types=1);
defined('MW_PATH') or exit('No direct script access allowed');

/**
 * Email_box_monitorsController
 *
 * Handles the actions for email box monitors related tasks
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.4.5
 */

class Email_box_monitorsController extends Controller
{
    /**
     * @return void
     * @throws CException
     */
    public function init()
    {
        parent::init();

        /** @var Customer $customer */
        $customer = customer()->getModel();

        if (!((int)$customer->getGroupOption('servers.max_email_box_monitors', 0))) {
            $this->redirect(['dashboard/index']);
        }

        // make sure the parent account has allowed access for this subaccount
        if (is_subaccount() && !subaccount()->canManageServers()) {
            $this->redirect(['dashboard/index']);
        }

        $this->addPageScript(['src' => AssetsUrl::js('email-box-monitors.js')]);
    }

    /**
     * @return array
     * @throws CException
     */
    public function filters()
    {
        $filters = [
            'postOnly + delete',
        ];

        return CMap::mergeArray($filters, parent::filters());
    }

    /**
     * List available email box monitors
     *
     * @return void
     * @throws CException
     */
    public function actionIndex()
    {
        /** @var Customer $customer */
        $customer = customer()->getModel();

        $server = new EmailBoxMonitor('search');
        $server->unsetAttributes();

        $server->attributes  = (array)request()->getQuery($server->getModelName(), []);
        $server->customer_id = (int)$customer->customer_id;

        $this->setData([
            'pageMetaTitle'   => $this->getData('pageMetaTitle') . ' | ' . t('servers', 'View email box monitors'),
            'pageHeading'     => t('servers', 'View email box monitors'),
            'pageBreadcrumbs' => [
                t('servers', 'Email box monitors') => createUrl('email_box_monitors/index'),
                t('app', 'View all'),
            ],
        ]);

        $this->render('list', compact('server'));
    }

    /**
     * Create a new email box monitor
     *
     * @return void
     * @throws CException
     */
    public function actionCreate()
    {
        /** @var Customer $customer */
        $customer = customer()->getModel();

        $server = new EmailBoxMonitor();
        $server->customer_id    = (int)$customer->customer_id;

        if (($limit = (int)$customer->getGroupOption('servers.max_email_box_monitors', 0)) > -1) {
            $count = EmailBoxMonitor::model()->countByAttributes(['customer_id' => (int)$customer->customer_id]);
            if ($count >= $limit) {
                notify()->addWarning(t('servers', 'You have reached the maximum number of allowed servers!'));
                $this->redirect(['email_box_monitors/index']);
            }
        }

        if (request()->getIsPostRequest() && ($attributes = (array)request()->getPost($server->getModelName(), []))) {
            if (!$server->isNewRecord && empty($attributes['password']) && isset($attributes['password'])) {
                unset($attributes['password']);
            }

            $server->attributes  = $attributes;
            $server->customer_id = (int)$customer->customer_id;

            if (!$server->testConnection() || !$server->save()) {
                notify()->addError(t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                notify()->addSuccess(t('app', 'Your form has been successfully saved!'));
            }

            hooks()->doAction('controller_action_save_data', $collection = new CAttributeCollection([
                'controller'=> $this,
                'success'   => notify()->getHasSuccess(),
                'server'    => $server,
            ]));

            if ($collection->itemAt('success')) {
                $this->redirect(['email_box_monitors/update', 'id' => $server->server_id]);
            }
        }

        $this->setData([
            'pageMetaTitle'   => $this->getData('pageMetaTitle') . ' | ' . t('servers', 'Create new email box monitor'),
            'pageHeading'     => t('servers', 'Create new email box monitor'),
            'pageBreadcrumbs' => [
                t('servers', 'Email box monitors') => createUrl('email_box_monitors/index'),
                t('app', 'Create new'),
            ],
        ]);

        $this->render('form', compact('server'));
    }

    /**
     * Update existing email box monitor
     *
     * @param string $id
     *
     * @return void
     * @throws CException
     * @throws CHttpException
     */
    public function actionUpdate($id)
    {
        /** @var Customer $customer */
        $customer = customer()->getModel();

        $server = EmailBoxMonitor::model()->findByAttributes([
            'server_id'   => (int)$id,
            'customer_id' => (int)$customer->customer_id,
        ]);

        if (empty($server)) {
            throw new CHttpException(404, t('app', 'The requested page does not exist.'));
        }

        if (!$server->getCanBeUpdated()) {
            $this->redirect(['email_box_monitors/index']);
        }

        if ($server->getIsLocked()) {
            notify()->addWarning(t('servers', 'This server is locked, you cannot change or delete it!'));
            $this->redirect(['email_box_monitors/index']);
        }

        if (request()->getIsPostRequest() && ($attributes = (array)request()->getPost($server->getModelName(), []))) {
            if (!$server->isNewRecord && empty($attributes['password']) && isset($attributes['password'])) {
                unset($attributes['password']);
            }

            $server->attributes  = $attributes;
            $server->customer_id = (int)$customer->customer_id;

            if (!$server->testConnection() || !$server->save()) {
                notify()->addError(t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                notify()->addSuccess(t('app', 'Your form has been successfully saved!'));
            }

            hooks()->doAction('controller_action_save_data', $collection = new CAttributeCollection([
                'controller'=> $this,
                'success'   => notify()->getHasSuccess(),
                'server'    => $server,
            ]));
        }

        $this->setData([
            'pageMetaTitle'   => $this->getData('pageMetaTitle') . ' | ' . t('servers', 'Update email box monitor'),
            'pageHeading'     => t('servers', 'Update email box monitor'),
            'pageBreadcrumbs' => [
                t('servers', 'Email box monitors') => createUrl('email_box_monitors/index'),
                t('app', 'Update'),
            ],
        ]);

        $this->render('form', compact('server'));
    }

    /**
     * Delete existing email box monitor
     *
     * @param string $id
     *
     * @return void
     * @throws CDbException
     * @throws CException
     * @throws CHttpException
     */
    public function actionDelete($id)
    {
        /** @var Customer $customer */
        $customer = customer()->getModel();

        $server = EmailBoxMonitor::model()->findByAttributes([
            'server_id'   => (int)$id,
            'customer_id' => (int)$customer->customer_id,
        ]);

        if (empty($server)) {
            throw new CHttpException(404, t('app', 'The requested page does not exist.'));
        }

        if ($server->getIsLocked()) {
            notify()->addWarning(t('servers', 'This server is locked, you cannot update, enable, disable, copy or delete it!'));
            if (!request()->getIsAjaxRequest()) {
                $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
            }
            app()->end();
        }

        if ($server->getCanBeDeleted()) {
            $server->delete();
        }

        if (!request()->getQuery('ajax')) {
            notify()->addSuccess(t('app', 'The item has been successfully deleted!'));
            $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
        }
    }

    /**
     * Create a copy of an existing email box monitor!
     *
     * @param string $id
     *
     * @return void
     * @throws CException
     * @throws CHttpException
     */
    public function actionCopy($id)
    {
        /** @var Customer $customer */
        $customer = customer()->getModel();

        $server = EmailBoxMonitor::model()->findByAttributes([
            'server_id'   => (int)$id,
            'customer_id' => (int)$customer->customer_id,
        ]);

        if (empty($server)) {
            throw new CHttpException(404, t('app', 'The requested page does not exist.'));
        }

        if ($server->getIsLocked()) {
            notify()->addWarning(t('servers', 'This server is locked, you cannot update, enable, disable, copy or delete it!'));
            if (!request()->getIsAjaxRequest()) {
                $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
            }
            app()->end();
        }

        if (($limit = (int)$customer->getGroupOption('servers.max_fbl_servers', 0)) > -1) {
            $count = EmailBoxMonitor::model()->countByAttributes(['customer_id' => (int)$customer->customer_id]);
            if ($count >= $limit) {
                notify()->addWarning(t('servers', 'You have reached the maximum number of allowed servers!'));
                if (!request()->getIsAjaxRequest()) {
                    $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
                }
                app()->end();
            }
        }

        if ($server->copy()) {
            notify()->addSuccess(t('servers', 'Your server has been successfully copied!'));
        } else {
            notify()->addError(t('servers', 'Unable to copy the server!'));
        }

        if (!request()->getIsAjaxRequest()) {
            $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
        }
    }

    /**
     * Enable a server that has been previously disabled.
     *
     * @param string $id
     *
     * @return void
     * @throws CHttpException
     * @throws CException
     */
    public function actionEnable($id)
    {
        /** @var Customer $customer */
        $customer = customer()->getModel();

        $server = EmailBoxMonitor::model()->findByAttributes([
            'server_id'   => (int)$id,
            'customer_id' => (int)$customer->customer_id,
        ]);

        if (empty($server)) {
            throw new CHttpException(404, t('app', 'The requested page does not exist.'));
        }

        if ($server->getIsLocked()) {
            notify()->addWarning(t('servers', 'This server is locked, you cannot update, enable, disable, copy or delete it!'));
            if (!request()->getIsAjaxRequest()) {
                $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
            }
            app()->end();
        }

        if ($server->getIsDisabled()) {
            $server->enable();
            notify()->addSuccess(t('servers', 'Your server has been successfully enabled!'));
        } else {
            notify()->addError(t('servers', 'The server must be disabled in order to enable it!'));
        }

        if (!request()->getIsAjaxRequest()) {
            $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
        }
    }

    /**
     * Disable a server that has been previously verified.
     *
     * @param string $id
     *
     * @return void
     * @throws CHttpException
     * @throws CException
     */
    public function actionDisable($id)
    {
        /** @var Customer $customer */
        $customer = customer()->getModel();

        $server = EmailBoxMonitor::model()->findByAttributes([
            'server_id'   => (int)$id,
            'customer_id' => (int)$customer->customer_id,
        ]);

        if (empty($server)) {
            throw new CHttpException(404, t('app', 'The requested page does not exist.'));
        }

        if ($server->getIsLocked()) {
            notify()->addWarning(t('servers', 'This server is locked, you cannot update, enable, disable, copy or delete it!'));
            if (!request()->getIsAjaxRequest()) {
                $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
            }
            app()->end();
        }

        if ($server->getIsActive()) {
            $server->disable();
            notify()->addSuccess(t('servers', 'Your server has been successfully disabled!'));
        } else {
            notify()->addError(t('servers', 'The server must be active in order to disable it!'));
        }

        if (!request()->getIsAjaxRequest()) {
            $this->redirect(request()->getPost('returnUrl', ['email_box_monitors/index']));
        }
    }

    /**
     * Export
     *
     * @return void
     */
    public function actionExport()
    {
        $models = EmailBoxMonitor::model()->findAllByAttributes([
            'customer_id' => (int)customer()->getId(),
        ]);

        if (empty($models)) {
            notify()->addError(t('app', 'There is no item available for export!'));
            $this->redirect(['index']);
        }

        // Set the download headers
        HeaderHelper::setDownloadHeaders('email-box-monitors.csv');

        try {
            $csvWriter  = League\Csv\Writer::createFromPath('php://output', 'w');
            $attributes = AttributeHelper::removeSpecialAttributes($models[0]->attributes, ['password']);

            /** @var callable $callback */
            $callback   = [$models[0], 'getAttributeLabel'];
            $attributes = array_map($callback, array_keys($attributes));

            $csvWriter->insertOne($attributes);

            foreach ($models as $model) {
                $attributes = AttributeHelper::removeSpecialAttributes($model->attributes, ['password']);
                $csvWriter->insertOne(array_values($attributes));
            }
        } catch (Exception $e) {
        }

        app()->end();
    }
}
