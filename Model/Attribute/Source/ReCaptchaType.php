<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source;

/**
 * Class ReCaptchaType
 * @package Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source
 */
class ReCaptchaType extends \Alekseon\AlekseonEav\Model\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\ReCaptchaAdminUi\Model\OptionSource\Type
     */
    protected $recaptchaTypeSource;

    /**
     * ReCaptchaType constructor.
     * @param \Magento\ReCaptchaAdminUi\Model\OptionSource $recaptchaTypeSource
     */
    public function __construct(
        \Magento\ReCaptchaAdminUi\Model\OptionSource $recaptchaTypeSource
    ) {
        $this->recaptchaTypeSource = $recaptchaTypeSource;
    }

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        $recaptchaTypes = $this->recaptchaTypeSource->toOptionArray();
        $options = [];
        foreach($recaptchaTypes as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
