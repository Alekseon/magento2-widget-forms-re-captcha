<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Observer;

use Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source\ReCaptchaType;
use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaCustomer\Model\AjaxLogin\ErrorProcessor;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;

/**
 * Class AjaxSendFriendObserver
 * @package Manolo\ReCaptcha\Observer
 */
class ValidateReCaptchaObserver implements ObserverInterface
{
    /**
     * @var CaptchaResponseResolverInterface
     */
    private $captchaResponseResolver;
    /**
     * @var ValidatorInterface
     */
    private $captchaValidator;
    /**
     * @var ErrorProcessor
     */
    private $errorProcessor;
    /**
     * @var \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver|ValidationConfigResolverInterface
     */
    private $validationConfigResolver;
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    private $captchaHelper;

    /**
     * ValidateReCaptchaObserver constructor.
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidatorInterface $captchaValidator
     * @param \Alekseon\WidgetFormsReCaptcha\Model\Ajax\ErrorProcessor $errorProcessor
     * @param \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver $validationConfigResolver
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidatorInterface $captchaValidator,
        \Alekseon\WidgetFormsReCaptcha\Model\Ajax\ErrorProcessor $errorProcessor,
        \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver $validationConfigResolver,
        \Magento\Captcha\Helper\Data $captchaHelper
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->captchaValidator = $captchaValidator;
        $this->errorProcessor = $errorProcessor;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaHelper = $captchaHelper;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute(Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $form = $controller->getForm();
        $reCaptchaType = $form->getRecaptchaType();

        if ($reCaptchaType) {
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            if ($reCaptchaType == ReCaptchaType::MAGENTO_CAPTCHA_VALUE) {
                $this->validateMagentoCaptcha($form, $request, $response);
            } else {
                $this->validateUiCaptcha($form, $request, $response);
            }
        }
    }

    /**
     * @param $controller
     */
    protected function validateMagentoCaptcha($form, $request, $response)
    {
        $formId = 'alekseon_widget_form_' . $form->getId();
        $captchaModel = $this->captchaHelper->getCaptcha($formId);

        if (!$this->captchaHelper->getConfig('enable')) {
            return false;
        }

        if (!$captchaModel->isCorrect($request->getPost("captcha_string"))) {
            $this->errorProcessor->processError(
                $response,
                __('Incorrect CAPTCHA')
            );
        }
    }

    /**
     * @param $form
     * @param $request
     * @param $response
     * @throws InputException
     */
    protected function validateUiCaptcha($form, $request, $response)
    {
        $reCaptchaType = $form->getRecaptchaType();
        $reCaptchaConfig = $this->validationConfigResolver->getByType($reCaptchaType);

        try {
            $reCaptchaResponse = $this->captchaResponseResolver->resolve($request);
        } catch (InputException $e) {
            $this->errorProcessor->processError(
                $response,
                $reCaptchaConfig->getValidationFailureMessage()
            );
            return;
        }

        $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $reCaptchaConfig);
        if (false === $validationResult->isValid()) {
            $this->errorProcessor->processError(
                $response,
                $reCaptchaConfig->getValidationFailureMessage()
            );
        }
    }
}
