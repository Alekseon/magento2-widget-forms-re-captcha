/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
define([
    'Magento_ReCaptchaFrontendUi/js/registry'
], function (
    reCaptchaRegistry
) {
    'use strict';

    return function (Component) {
        return Component.extend({
            onComplete: function () {
                this.resetRecaptcha();
                this._super();
            },

            resetRecaptcha: function () {
                var
                    i,
                    captchaList = reCaptchaRegistry.captchaList(),
                    tokenFieldsList = reCaptchaRegistry.tokenFields();

                for (i = 0; i < captchaList.length; i++) {
                    grecaptcha.reset(captchaList[i]);

                    if (tokenFieldsList[i]) {
                        tokenFieldsList[i].value = '';
                    }
                }
            }
        });
    };
});
