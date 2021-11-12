<?php declare(strict_types=1);
defined('MW_PATH') or exit('No direct script access allowed');

/**
 * ListFieldBuilderTypeCrud
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 2.0.0
 */

/**
 * @property CWidget $owner
 */
abstract class ListFieldBuilderTypeCrud extends CBehavior
{
    /**
     * @param CEvent $event
     *
     * @return void
     */
    abstract public function _saveFields(CEvent $event);

    /**
     * @param CEvent $event
     *
     * @return void
     */
    abstract public function _displayFields(CEvent $event);

    /**
     * @return void
     * @throws Exception
     */
    abstract public function _registerJavascriptTemplate();

    /**
     * @param CEvent $event
     *
     * @return void
     */
    abstract public function _addReadOnlyAttributes(CEvent $event);

    /**
     * @return void
     */
    public function _renderAddButton()
    {
        try {
            $baseFolder  = dirname((string)(new ReflectionClass($this))->getFileName());
            $viewsFolder = $baseFolder . '/../views/';
        } catch (Exception $e) {
            $viewsFolder = '';
        }

        $this->owner->renderInternal($viewsFolder . 'add-button.php');
    }

    /**
     * @param ListField $model
     *
     * @return array
     */
    abstract protected function buildFieldArray(ListField $model): array;
}
