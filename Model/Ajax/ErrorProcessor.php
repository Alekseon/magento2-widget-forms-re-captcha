<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetFormsReCaptcha\Model\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class ErrorProcessor
 * @package Alekseon\WidgetFormsReCaptcha\Model\Ajax
 */
class ErrorProcessor
{
    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ActionFlag $actionFlag,
        SerializerInterface $serializer
    ) {
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
    }

    /**
     * Set "no dispatch" flag and error message to Response
     *
     * @param ResponseInterface $response
     * @param string $message
     * @return void
     */
    public function processError(ResponseInterface $response, $message)
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $jsonPayload = $this->serializer->serialize($this->getResponseData($message));

        $this->setResponseCode($response)
            ->representJson($jsonPayload);
    }

    /**
     * @param $message
     * @return array
     */
    protected function getResponseData($message)
    {
        return [
            'errors' => true,
            'message' => $message,
        ];
    }

    /**
     *
     */
    protected function setResponseCode($response)
    {
        $response->setHttpResponseCode(500);
        return $response;
    }
}
