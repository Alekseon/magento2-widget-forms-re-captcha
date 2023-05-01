<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Observer;

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
            $dataObject = $observer->getEvent()->getDataObject();
            $widgetBlock = $observer->getEvent()->getWidgetBlock();
            $children = $dataObject->getUiComponentChildren();

            $recaptchaBlock = $widgetBlock->addChild(
                'recaptcha',
                \Alekseon\WidgetFormsReCaptcha\Block\ReCaptcha::class
            );
            $recaptchaBlock->setWidgetForm($form);

            if (!$recaptchaBlock->isRecaptchaEnabled()) {
                return;
            }

            $reCaptchaJSON = json_decode($recaptchaBlock->getJsLayout(), true);
            $reCaptchaComponents = $reCaptchaJSON['components'];

            foreach ($reCaptchaComponents as $componentId => $component) {
                $children[$componentId] = $component;
            }

            $dataObject->setUiComponentChildren($children);
        }
    }
}
