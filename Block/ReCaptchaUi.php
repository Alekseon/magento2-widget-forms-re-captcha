<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Block;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;

/**
 *
 */
class ReCaptchaUi extends \Magento\ReCaptchaUi\Block\ReCaptcha implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'Magento_ReCaptchaFrontendUi::recaptcha.phtml';

    /**
     * @var
     */
    private $widgetForm;
    /**
     * @var \Alekseon\WidgetFormsReCaptcha\Model\UiConfigResolver
     */
    private $captchaUiConfigResolver;

    /**
     * @param Template\Context $context
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param Json $serializer
     * @param \Alekseon\WidgetFormsReCaptcha\Model\UiConfigResolver $captchaUiConfigResolver
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        Json $serializer,
        \Alekseon\WidgetFormsReCaptcha\Model\UiConfigResolver $captchaUiConfigResolver,
        array $data = []
    )
    {
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        parent::__construct($context, $captchaUiConfigResolver, $isCaptchaEnabled, $serializer, $data);
    }

    /**
     * @param $widgetForm
     * @return ReCaptcha $this
     */
    public function setWidgetForm($widgetForm)
    {
        $this->widgetForm = $widgetForm;
        return $this;
    }

    /**
     * @return string | bool
     */
    private function getRecaptchaType()
    {
        return $this->widgetForm ? $this->widgetForm->getRecaptchaType() : false;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getCaptchaUiConfig(): array
    {
        return $this->captchaUiConfigResolver->getByType($this->getRecaptchaType());
    }

    /**
     *
     */
    public function getJsLayout()
    {
        $components = [];

        $components['recaptcha'] = [
            'component' => 'Magento_ReCaptchaFrontendUi/js/reCaptcha'
        ];

        $this->jsLayout = [
            'components' => $components,
        ];

        return parent::getJsLayout();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    public function toHtml()
    {
        return \Magento\Framework\View\Element\Template::toHtml();
    }
}
