/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
define([
    'Magento_Captcha/js/model/captchaList'
], function (
    magentoCaptchaList
) {
    'use strict';

    return function (widget) {
        $.widget('mage.alekseonWidgetForm', widget, {
            onComplete: function () {
                console.log('mage.alekseonWidgetForm');
            }
        });

        return $.mage.alekseonWidgetForm;
    };
});
