<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Observer;

use Alekseon\WidgetForms\Block\WidgetForm;
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
        if ($form->getRecaptchaType()) {
            /** @var WidgetForm $widgetBlock */
            $widgetBlock = $observer->getEvent()->getWidgetBlock();

             $recaptchaBlock = $widgetBlock->addChild(
                'recaptcha',
                \Alekseon\WidgetFormsReCaptcha\Block\ReCaptcha::class
            );
            $recaptchaBlock->setWidgetForm($form);

            if (!$recaptchaBlock->isRecaptchaEnabled()) {
                return;
            }

            $widgetBlock->setChild('recaptcha.container', $recaptchaBlock);
        }
    }
}
