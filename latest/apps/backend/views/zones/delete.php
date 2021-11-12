<?php declare(strict_types=1);
defined('MW_PATH') or exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.3.4.5
 */

/** @var Controller $controller */
$controller = controller();

/** @var string $pageHeading */
$pageHeading = (string)$controller->getData('pageHeading');

/** @var Zone $zone */
$zone = $controller->getData('zone');

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->getData()}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->add('renderContent', false)}
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
try {
    hooks()->doAction('before_view_file_content', $viewCollection = new CAttributeCollection([
        'controller'    => $controller,
        'renderContent' => true,
    ]));
} catch (Exception $e) {
}

// and render if allowed
if (!empty($viewCollection) && $viewCollection->itemAt('renderContent')) { ?>
    <?php echo CHtml::form(); ?>
    <div class="box box-primary borderless">
        <div class="box-header">
            <div class="pull-left">
                <h3 class="box-title">
                    <?php echo IconHelper::make('glyphicon-remove-circle') . html_encode((string)$pageHeading); ?>
                </h3>
            </div>
            <div class="pull-right">
                <?php echo HtmlHelper::accessLink(IconHelper::make('cancel') . t('app', 'Cancel'), ['zones/index'], ['class' => 'btn btn-primary btn-flat', 'title' => t('app', 'Cancel')]); ?>
            </div>
            <div class="clearfix"><!-- --></div>
        </div>
        <div class="box-body">
            <hr />
            
            <div class="alert alert-danger alert-dismissable">
                <i class="fa fa-ban"></i>
                <strong>
                    <?php echo t('zones', 'Please note that removing this zone will also remove every record that depends on it, such as taxes, customer companies, etc!'); ?>
                    <br />
                    <?php echo t('zones', 'Are you still sure you want to remove this zone? There is no coming back after you do it!'); ?>
                </strong>
            </div>

            <hr />
        </div>
        <div class="box-footer">
            <div class="pull-right">
                <button type="submit" class="btn btn-danger btn-submit"><?php echo t('app', 'I understand, delete it!'); ?></button>
            </div>
            <div class="clearfix"><!-- --></div>
        </div>
    </div>
    <?php echo CHtml::endForm(); ?>
<?php
}
/**
 * This hook gives a chance to append content after the view file default content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->getData()}
 * @since 1.3.3.1
 */
try {
    hooks()->doAction('after_view_file_content', new CAttributeCollection([
        'controller'      => $controller,
        'renderedContent' => !empty($viewCollection) ? $viewCollection->itemAt('renderContent') : false,
    ]));
} catch (Exception $e) {
}