<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Model;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\CaptchaTypeResolverInterface;
use Magento\ReCaptchaUi\Model\UiConfigProviderInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

/**
 * Class UiConfigResolver
 * @package Alekseon\WidgetFormsReCaptcha\Model
 */
class UiConfigResolver extends \Magento\ReCaptchaUi\Model\UiConfigResolver
{
    /**
     * @var array
     */
    private $uiConfigProviders;

    /**
     * UiConfigResolver constructor.
     * @param CaptchaTypeResolverInterface $captchaTypeResolver
     * @param array $uiConfigProviders
     * @throws InputException
     */
    public function __construct(CaptchaTypeResolverInterface $captchaTypeResolver, array $uiConfigProviders = [])
    {
        foreach ($uiConfigProviders as $uiConfigProvider) {
            if (!$uiConfigProvider instanceof UiConfigProviderInterface) {
                throw new InputException(
                    __('UI config provider must implement %1', [ UiConfigResolverInterface::class])
                );
            }
        }
        $this->uiConfigProviders = $uiConfigProviders;

        parent::__construct($captchaTypeResolver, $uiConfigProviders);
    }

    /**
     * @inheritdoc
     */
    public function getByType($captchaType)
    {
        if (!isset($this->uiConfigProviders[$captchaType])) {
            throw new InputException(
                __('UI config provider for "%type" is not configured.', ['type' => $captchaType])
            );
        }
        return $this->uiConfigProviders[$captchaType]->get();
    }
}
