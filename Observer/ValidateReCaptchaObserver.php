<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Observer;

use Alekseon\CustomFormsBuilder\Model\Form;
use Alekseon\WidgetForms\Controller\Form\Submit;
use Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source\ReCaptchaType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\ErrorMessageConfigInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Model\ValidationErrorMessagesProvider;
use Psr\Log\LoggerInterface;

/**
 * Class AjaxSendFriendObserver
 * @package Manolo\ReCaptcha\Observer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var \Alekseon\WidgetFormsReCaptcha\Model\Ajax\ErrorProcessor
     */
    private $errorProcessor;
    /**
     * @var \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver|ValidationConfigResolverInterface
     */
    private $validationConfigResolver;
    /**
     * @var ValidationErrorMessagesProvider
     */
    private $validationErrorMessagesProvider;
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    private $captchaHelper;
    /**
     * @var ErrorMessageConfigInterface
     */
    private $errorMessageConfig;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ValidateReCaptchaObserver constructor.
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidatorInterface $captchaValidator
     * @param ErrorMessageConfigInterface $errorMessageConfig
     * @param ValidationErrorMessagesProvider $validationErrorMessagesProvider
     * @param \Alekseon\WidgetFormsReCaptcha\Model\Ajax\ErrorProcessor $errorProcessor
     * @param \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver $validationConfigResolver
     * @param \Magento\Captcha\Helper\Data $captchaHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidatorInterface $captchaValidator,
        ErrorMessageConfigInterface $errorMessageConfig,
        ValidationErrorMessagesProvider $validationErrorMessagesProvider,
        \Alekseon\WidgetFormsReCaptcha\Model\Ajax\ErrorProcessor $errorProcessor,
        \Alekseon\WidgetFormsReCaptcha\Model\ValidationConfigResolver $validationConfigResolver,
        \Magento\Captcha\Helper\Data $captchaHelper,
        LoggerInterface $logger
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->captchaValidator = $captchaValidator;
        $this->errorMessageConfig = $errorMessageConfig;
        $this->validationErrorMessagesProvider = $validationErrorMessagesProvider;
        $this->errorProcessor = $errorProcessor;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaHelper = $captchaHelper;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute(Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $form = $controller->getForm();
        $this->validateCaptcha($form, $controller);
    }

    /**
     * @param Form $form
     * @param Submit $controller
     * @param bool $isValidated
     * @return void
     * @throws InputException
     */
    public function validateCaptcha(Form $form, Submit $controller, bool $isValidated = false)
    {
        if ($isValidated) {
            return;
        }
        $reCaptchaType = $form->getRecaptchaType();
        if (!$reCaptchaType) {
            return;
        }
        $request = $controller->getRequest();
        $response = $controller->getResponse();
        switch ($reCaptchaType) {
            case ReCaptchaType::MAGENTO_CAPTCHA_VALUE:
                $this->validateMagentoCaptcha($form, $request, $response);
                break;
            default:
                $this->validateUiCaptcha($form, $request, $response);
        }
    }

    /**
     * @param $form
     * @param $request
     * @param $response
     * @return void
     */
    protected function validateMagentoCaptcha($form, $request, $response)
    {
        $formId = 'alekseon-widget-form-' . $form->getId();
        $captchaModel = $this->captchaHelper->getCaptcha($formId);

        if (!$this->captchaHelper->getConfig('enable')) {
            return;
        }

        $captcha = $request->getPost('captcha');
        $captchaString = $captcha[$formId] ?? '';

        if (!$captchaModel->isCorrect($captchaString)) {
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
            $this->processError($response, [], $reCaptchaType);
            return;
        }

        $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $reCaptchaConfig);
        if (false === $validationResult->isValid()) {
            $this->processError($response, $validationResult->getErrors(), $reCaptchaType);
        }
    }

    /**
     * @param $response
     * @param array $errorMessages
     * @param string $sourceKey
     * @return void
     */
    public function processError($response, array $errorMessages, string $sourceKey)
    {
        $validationErrorText = $this->errorMessageConfig->getValidationFailureMessage();
        $technicalErrorText = $this->errorMessageConfig->getTechnicalFailureMessage();

        $message = $errorMessages ? $validationErrorText : $technicalErrorText;

        foreach ($errorMessages as $errorMessageCode => $errorMessageText) {
            if (!$this->isValidationError($errorMessageCode)) {
                $message = $technicalErrorText;
                $this->logger->error(
                    __(
                        'reCAPTCHA \'%1\' form error: %2',
                        $sourceKey,
                        $errorMessageText
                    )
                );
            }
        }

        $this->errorProcessor->processError($response, $message);
    }

    /**
     * @param string $errorMessageCode
     * @return bool
     */
    private function isValidationError(string $errorMessageCode): bool
    {
        return $errorMessageCode !== $this->validationErrorMessagesProvider->getErrorMessage($errorMessageCode);
    }
}
