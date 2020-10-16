<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetFormsReCaptcha\Observer;

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
    protected $captchaResponseResolver;
    /**
     * @var ValidatorInterface
     */
    protected $captchaValidator;
    /**
     * @var IsCaptchaEnabledInterface
     */
    protected $isCaptchaEnabled;
    /**
     * @var ErrorProcessor
     */
    protected $errorProcessor;
    /**
     * @var \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver|ValidationConfigResolverInterface
     */
    protected $validationConfigResolver;

    /**
     * ValidateReCaptchaObserver constructor.
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidatorInterface $captchaValidator
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param \Alekseon\WidgetFormsReCaptcha\Model\Ajax\ErrorProcessor $errorProcessor
     * @param \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver $validationConfigResolver
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidatorInterface $captchaValidator,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        \Alekseon\WidgetFormsReCaptcha\Model\Ajax\ErrorProcessor $errorProcessor,
        \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver $validationConfigResolver
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->captchaValidator = $captchaValidator;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->errorProcessor = $errorProcessor;
        $this->validationConfigResolver = $validationConfigResolver;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute(Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $form = $controller->getForm();
        if ($reCaptchaType = $form->getRecaptchaType()) {

            $request = $controller->getRequest();
            $response = $controller->getResponse();

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
}
