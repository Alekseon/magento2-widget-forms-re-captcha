<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetFormsReCaptcha\Model;

/**
 * Class CaptchaConfigProvider
 * @package Alekseon\WidgetFormsReCaptcha\Model
 */
class CaptchaConfigProvider
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $captchaData;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param array $formIds
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Captcha\Helper\Data $captchaData
    ) {
        $this->storeManager = $storeManager;
        $this->captchaData = $captchaData;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'isCaseSensitive' => $this->isCaseSensitive(),
            'imageHeight' => $this->getImageHeight(),
            'imageSrc' => $this->getImageSrc(),
            'refreshUrl' => $this->getRefreshUrl(),
            'isRequired' => $this->isRequired(),
            'timestamp' => time()
        ];
    }

    protected function getImageHeight()
    {
        return $this->getCaptchaModel()->getHeight();
    }

    /**
     * @return bool
     */
    protected function isCaseSensitive()
    {
        return (boolean)$this->getCaptchaModel()->isCaseSensitive();
    }

    /**
     * @return bool
     */
    protected function isRequired()
    {
        if (!$this->captchaData->getConfig('enable')) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getImageSrc()
    {
        if ($this->isRequired()) {
            $captcha = $this->getCaptchaModel();
            $captcha->generate();
            return $captcha->getImgSrc();
        }
        return '';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getRefreshUrl()
    {
        $store = $this->storeManager->getStore();
        return $store->getUrl('captcha/refresh', ['_secure' => $store->isCurrentlySecure()]);
    }

    /**
     * @param $formId
     * @return \Magento\Captcha\Model\CaptchaInterface
     */
    protected function getCaptchaModel()
    {
        return $this->captchaData->getCaptcha('alekseon_widget_form');
    }
}
