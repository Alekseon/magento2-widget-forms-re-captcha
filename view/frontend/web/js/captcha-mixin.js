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
        $.widget('mage.captcha', widget, {
            _create: function () {
                this.formId = this.options.type;
                magentoCaptchaList.add(this);
                this._super();
            }
        });

        return $.mage.captcha;
    };
});
