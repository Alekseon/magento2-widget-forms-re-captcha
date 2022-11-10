<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetFormsReCaptcha\Block;

use Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source\ReCaptchaType;
use Alekseon\WidgetFormsReCaptcha\Model\CaptchaConfigProvider;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;

/**
 * Class ReCaptcha
 * @package Alekseon\WidgetFormsReCaptcha\Block
 */
class ReCaptcha extends \Magento\ReCaptchaUi\Block\ReCaptcha implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'Magento_ReCaptchaFrontendUi::recaptcha.phtml';
    /**
     * @var
     */
    protected $widgetForm;
    /**
     * @var \Alekseon\WidgetFormsReCaptcha\Model\UiConfigResolver
     */
    protected $captchaUiConfigResolver;
    /**
     * @var CaptchaConfigProvider
     */
    protected $captchaConfigProvider;

    /**
     * ReCaptcha constructor.
     * @param Template\Context $context
     * @param \Alekseon\WidgetFormsReCaptcha\Model\UiConfigResolver $captchaUiConfigResolver
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param Json $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Alekseon\WidgetFormsReCaptcha\Model\UiConfigResolver $captchaUiConfigResolver,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        Json $serializer,
        CaptchaConfigProvider $captchaConfigProvider,
        array $data = []
    )
    {
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->captchaConfigProvider = $captchaConfigProvider;
        parent::__construct($context, $captchaUiConfigResolver, $isCaptchaEnabled, $serializer, $data);
    }

    /**
     * @param $widgetForm
     * @return $this
     */
    public function setWidgetForm($widgetForm)
    {
        $this->widgetForm = $widgetForm;
        return $this;
    }

    /**
     * @return bool
     */
    protected function getRecaptchaType()
    {
        if ($this->widgetForm) {
            return $this->widgetForm->getRecaptchaType();
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function isReCaptchaEnabled()
    {
        if ($this->getRecaptchaType()) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getCaptchaUiConfig(): array
    {
        $uiConfig = [];
        if ($this->getRecaptchaType() !== ReCaptchaType::MAGENTO_CAPTCHA_VALUE) {
            $uiConfig = $this->captchaUiConfigResolver->getByType($this->getRecaptchaType());
        }
        return $uiConfig;
    }

    /**
     *
     */
    public function getJsLayout()
    {
        if ($this->getRecaptchaType() == ReCaptchaType::MAGENTO_CAPTCHA_VALUE) {
            $this->jsLayout =
                [
                    'components' =>
                        [
                            'recaptcha' => [
                                'component' => 'Alekseon_WidgetFormsReCaptcha/js/view/checkout/widgetFormCaptcha',
                                'formId' => 'alekseon_widget_form_' . $this->widgetForm->getId(),
                                'configSource' => 'alekseon_widget_form',
                                'alekseon_widget_form' => [
                                    'captcha' => $this->captchaConfigProvider->getConfig(),
                                ],
                            ]
                        ]
                ];
        } else {
            $this->jsLayout =
                [
                    'components' =>
                        [
                            'recaptcha' => [
                                'component' => 'Magento_ReCaptchaFrontendUi/js/reCaptcha'
                            ]
                        ]
                ];
        }

        return parent::getJsLayout();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    public function toHtml()
    {
        if (!$this->isReCaptchaEnabled()) {
            return '';
        }

        return \Magento\Framework\View\Element\Template::toHtml();
    }
}
