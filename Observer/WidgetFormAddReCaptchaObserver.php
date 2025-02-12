<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Observer;

use Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source\ReCaptchaType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class WidgetFormAddReCaptchaObserver
 * @package Alekseon\WidgetFormsReCaptcha\Observer
 */
class WidgetFormAddReCaptchaObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $form = $observer->getEvent()->getForm();

        if (!$form->getRecaptchaType()) {
            return;
        }

        switch ($form->getRecaptchaType()) {
            case ReCaptchaType::MAGENTO_CAPTCHA_VALUE:
                $recaptchaBlockClass = \Alekseon\WidgetFormsReCaptcha\Block\DefaultCaptcha::class;
                break;
            case ReCaptchaType::CLOUDFLARE_CAPTCHA_VALUE:
                $recaptchaBlockClass = '\PixelOpen\CloudflareTurnstile\Block\Turnstile';
                break;
            default:
                $recaptchaBlockClass = \Alekseon\WidgetFormsReCaptcha\Block\ReCaptchaUi::class;
        }

        $widgetBlock = $observer->getEvent()->getWidgetBlock();
        $lastTab = $widgetBlock->getTabBlock($widgetBlock->getTabsCounter());
        $recaptchaBlock = $widgetBlock->addChild(
            'recaptcha',
            $recaptchaBlockClass,
        );

        if ($form->getRecaptchaType() == ReCaptchaType::CLOUDFLARE_CAPTCHA_VALUE) {
            $recaptchaBlock->setAction('alekseon_widget_form');
        }

        $recaptchaBlock->setWidgetForm($form);
        $lastTab->setChild('recaptcha.container', $recaptchaBlock);
    }
}
