<?php declare(strict_types=1);
defined('MW_PATH') or exit('No direct script access allowed');

/**
 * SurveyFieldCheckbox
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.7.8
 */

/**
 * Class SurveyFieldCheckbox
 */
class SurveyFieldCheckbox extends SurveyField
{
    /**
     * @var string
     */
    public $check_value = '1';

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            ['check_value', 'required'],
            ['check_value', 'length', 'min' => 1, 'max' => 255],
        ];

        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $labels = [
            'check_value' => t('survey_fields', 'Value when checked'),
        ];

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SurveyField the static model class
     */
    public static function model($className=__CLASS__)
    {
        /** @var SurveyField $model */
        $model = parent::model($className);

        return $model;
    }

    /**
     * @return array
     */
    public function attributeHelpTexts()
    {
        $texts = [
            'check_value' => t('survey_fields', 'The value of the field when checked.'),
        ];

        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }

    /**
     * @return string
     */
    public function getCheckValue(): string
    {
        return isset($this->check_value) ? (string)$this->check_value : '1';
    }

    /**
     * @return bool
     */
    protected function beforeSave()
    {
        $this->modelMetaData->getModelMetaData()->add('check_value', (string)$this->check_value);
        return parent::beforeSave();
    }

    /**
     * @return void
     */
    protected function afterConstruct()
    {
        parent::afterConstruct();
        $this->check_value = (string)$this->modelMetaData->getModelMetaData()->itemAt('check_value');
    }

    /**
     * @return void
     */
    protected function afterFind()
    {
        $this->check_value = (string)$this->modelMetaData->getModelMetaData()->itemAt('check_value');
        parent::afterFind();
    }
}
