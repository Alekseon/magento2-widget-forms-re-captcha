<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source;

/**
 * Class ReCaptchaType
 * @package Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source
 */
class ReCaptchaType extends \Alekseon\AlekseonEav\Model\Attribute\Source\AbstractSource
{
    const MAGENTO_CAPTCHA_VALUE = 'magento_captcha';

    /**
     * @var \Magento\ReCaptchaAdminUi\Model\OptionSource
     */
    protected $recaptchaTypeSource;
    protected $moduleManager;

    /**
     * ReCaptchaType constructor.
     * @param \Magento\ReCaptchaAdminUi\Model\OptionSource $recaptchaTypeSource
     */
    public function __construct(
        \Magento\ReCaptchaAdminUi\Model\OptionSource $recaptchaTypeSource,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->recaptchaTypeSource = $recaptchaTypeSource;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return array
     */
    public function getUiRecaptchaOptions()
    {
        $recaptchaTypes = $this->recaptchaTypeSource->toOptionArray();
        $options = [];
        foreach($recaptchaTypes as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        $options = [];
        $options[self::MAGENTO_CAPTCHA_VALUE] = __('Magento Captcha');
        $uiRecaptchaOptions = $this->getUiRecaptchaOptions();
        $options = array_merge($options, $uiRecaptchaOptions);
        return $options;
    }
}
