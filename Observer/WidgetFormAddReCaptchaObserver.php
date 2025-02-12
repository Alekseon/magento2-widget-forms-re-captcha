<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Observer;

use Alekseon\CustomFormsBuilder\Model\Form;
use Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source\ReCaptchaType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template;

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
        /** @var Form $form */
        $form = $observer->getEvent()->getForm();

        if (!$form->getRecaptchaType()) {
            return;
        }

        $recaptchaBlockClass = $this->getRecaptchaBlockClass($form);

        $widgetBlock = $observer->getEvent()->getWidgetBlock();
        $recaptchaBlock = $widgetBlock->addChild(
            'recaptcha',
            $recaptchaBlockClass,
        );
        $recaptchaBlock->setWidgetForm($form);

        $this->prepareRecaptchaBlock($form, $recaptchaBlock);

        $lastTab = $widgetBlock->getTabBlock($widgetBlock->getTabsCounter());
        $lastTab->setChild('recaptcha.container', $recaptchaBlock);
    }

    /**
     * This method is used by plugins
     *
     * @param Form $form
     * @param Template $recaptchaBlock
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareRecaptchaBlock(Form $form, Template $recaptchaBlock)
    {
        return $this;
    }

    /**
     * @param Form $form
     * @return string
     */
    public function getRecaptchaBlockClass(Form $form)
    {
        switch ($form->getRecaptchaType()) {
            case ReCaptchaType::MAGENTO_CAPTCHA_VALUE:
                $recaptchaBlockClass = \Alekseon\WidgetFormsReCaptcha\Block\DefaultCaptcha::class;
                break;
            default:
                $recaptchaBlockClass = \Alekseon\WidgetFormsReCaptcha\Block\ReCaptchaUi::class;
        }
        return $recaptchaBlockClass;
    }
}
