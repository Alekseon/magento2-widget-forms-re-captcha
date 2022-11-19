/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Captcha/js/view/checkout/defaultCaptcha',
    'Magento_Captcha/js/model/captchaList',
    'Magento_Captcha/js/model/captcha'
],
function (defaultCaptcha, captchaList, Captcha) {
    'use strict';

    return defaultCaptcha.extend({
        /** @inheritdoc */
        initialize: function () {
            var self = this,
                captchaData,
                currentCaptcha;

            this._super();

            captchaData = this.alekseon_widget_form.captcha;

            var captcha;
            captchaData.formId = this.formId;
            captcha = Captcha(captchaData);
            captcha.setIsVisible(true);
            this.setCurrentCaptcha(captcha);
            captchaList.add(captcha);
        }
    });
});
