<?php declare(strict_types=1);
defined('MW_PATH') or exit('No direct script access allowed');

/**
 * CampaignCollection
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 2.0.0
 */

class CampaignCollection extends BaseCollection
{
    /**
     * @param mixed $condition
     *
     * @return CampaignCollection
     */
    public static function findAll($condition = ''): self
    {
        return new self(Campaign::model()->findAll($condition));
    }

    /**
     * @param array $attributes
     *
     * @return CampaignCollection
     */
    public static function findAllByAttributes(array $attributes): self
    {
        return new self(Campaign::model()->findAllByAttributes($attributes));
    }
}
