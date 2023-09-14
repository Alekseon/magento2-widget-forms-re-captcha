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
            this._super();
            let captchaData = this.alekseon_widget_form.captcha;
            captchaData.formId = this.formId;
            let captcha = Captcha(captchaData);
            captcha.setIsVisible(true);
            this.setCurrentCaptcha(captcha);
            captcha.refresh();
            captchaList.add(captcha);
        }
    });
});
