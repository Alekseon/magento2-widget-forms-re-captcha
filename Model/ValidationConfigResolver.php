<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Model;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\CaptchaTypeResolverInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigProviderInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;

/**
 * Class ValidationConfigResolver
 * @package Alekseon\WidgetFormsReCaptcha\Model
 */
class ValidationConfigResolver extends \Magento\ReCaptchaUi\Model\ValidationConfigResolver
{
    /**
     * @var CaptchaTypeResolverInterface
     */
    protected $captchaTypeResolver;

    /**
     * @var ValidationConfigProviderInterface[]
     */
    protected $validationConfigProviders;

    /**
     * @param CaptchaTypeResolverInterface $captchaTypeResolver
     * @param ValidationConfigProviderInterface[] $validationConfigProviders
     * @throws InputException
     */
    public function __construct(
        CaptchaTypeResolverInterface $captchaTypeResolver,
        array $validationConfigProviders = []
    ) {
        $this->captchaTypeResolver = $captchaTypeResolver;

        foreach ($validationConfigProviders as $validationConfigProvider) {
            if (!$validationConfigProvider instanceof ValidationConfigProviderInterface) {
                throw new InputException(
                    __('Validation config provider must implement %1.', [ConfigProviderInterface::class])
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
