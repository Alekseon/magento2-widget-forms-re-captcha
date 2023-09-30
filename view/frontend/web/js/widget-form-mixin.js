/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
define([
    'jquery',
    'Magento_Captcha/js/model/captchaList'
], function (
    $,
    magentoCaptchaList
) {
    'use strict';

    return function (widget) {
        $.widget('mage.alekseonWidgetForm', widget, {
            onComplete: function () {
                var currentCaptcha;
                currentCaptcha = magentoCaptchaList.getCaptchaByFormId(this.options.formId);
                if (currentCaptcha != null) {
                    currentCaptcha.refresh();
                }

                this._super();
            }
        });

        return $.mage.alekseonWidgetForm;
    };
});
