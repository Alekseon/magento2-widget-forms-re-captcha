/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
var config = {
    config: {
        mixins: {
            'Alekseon_WidgetForms/js/widget-form': {
                'Alekseon_WidgetFormsReCaptcha/js/widget-form-mixin': true
            },
            'Magento_Captcha/js/captcha': {
                'Alekseon_WidgetFormsReCaptcha/js/captcha-mixin': true
            }
        }
    }
};
