<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetFormsReCaptcha\Plugin;

class CloudflareTurnstileConfigPlugin
{
    /**
     * @param $subject
     * @param $config
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetTurnstileConfig($subject, $config)
    {
        $config['config']['forms'][] = 'alekseon_widget_form';
        return $config;
    }
}
