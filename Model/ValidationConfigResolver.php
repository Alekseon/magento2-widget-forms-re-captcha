<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Model;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\ValidationConfigProviderInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;

/**
 * Class ValidationConfigResolver
 * @package Alekseon\WidgetFormsReCaptcha\Model
 */
class ValidationConfigResolver extends \Magento\ReCaptchaUi\Model\ValidationConfigResolver
{
    /**
     * @var ValidationConfigProviderInterface[]
     */
    private $validationConfigProviders;

    /**
     * @param ValidationConfigProviderInterface[] $validationConfigProviders
     * @throws InputException
     */
    public function __construct(
        array $validationConfigProviders = []
    ) {
        foreach ($validationConfigProviders as $validationConfigProvider) {
            if (!$validationConfigProvider instanceof ValidationConfigProviderInterface) {
                throw new InputException(
                    __('Validation config provider must implement %1.', [ValidationConfigProviderInterface::class])
                );
            }
        }
        $this->validationConfigProviders = $validationConfigProviders;
    }

    /**
     * @param $captchaType
     * @return ValidationConfigInterface
     * @throws InputException
     */
    public function getByType($captchaType)
    {
        if (!isset($this->validationConfigProviders[$captchaType])) {
            throw new InputException(
                __('Validation config provider for "%type" is not configured.', ['type' => $captchaType])
            );
        }
        return $this->validationConfigProviders[$captchaType]->get();
    }
}
