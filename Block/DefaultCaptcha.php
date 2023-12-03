<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Block;

/**
 *
 */
class DefaultCaptcha extends \Magento\Captcha\Block\Captcha\DefaultCaptcha
{
    private $widgetForm;

    /**
     * @param $widgetForm
     * @return $this
     */
    public function setWidgetForm($widgetForm)
    {
        $this->widgetForm = $widgetForm;
        $this->setFormId('alekseon-widget-form-' . $widgetForm->getId());
        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->getCaptchaModel()->generate();
        return \Magento\Framework\View\Element\Template::_toHtml();
    }
}
