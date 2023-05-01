<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetFormsReCaptcha\Setup\Patch\Data;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;
use Alekseon\CustomFormsBuilder\Model\FormFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class CreateRecaptchaTypeAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var \Alekseon\AlekseonEav\Setup\EavDataSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository
     */
    private $formAttributeRepository;
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Alekseon\AlekseonEav\Setup\EavDataSetupFactory $eavSetupFactory
     * @param \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository $formAttributeRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Alekseon\AlekseonEav\Setup\EavDataSetupFactory $eavSetupFactory,
        \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository $formAttributeRepository,
        FormFactory $formFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->formAttributeRepository = $formAttributeRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);

        $eavSetup->createAttribute(
            'recaptcha_type',
            [
                'frontend_input' => 'select',
                'frontend_label' => 'ReCaptcha Type',
                'backend_type' => 'varchar',
                'source_model' => 'Alekseon\WidgetFormsReCaptcha\Model\Attribute\Source\ReCaptchaType',
                'visible_in_grid' => false,
                'is_required' => false,
                'sort_order' => 50,
                'group_code' => 'widget_form_attribute',
                'scope' => Scopes::SCOPE_WEBSITE,
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

        /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);
        $eavSetup->deleteAttribute('recaptcha_type');
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
