<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Block_Adminhtml_Composer_Packages_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $package = Mage::registry('current_package');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('genmato_composerrepo')->__('Package Information')));

        $fieldset->addField('name', 'text',
            array(
                'name'  => 'name',
                'label' => Mage::helper('genmato_composerrepo')->__('Package Name'),
                'title' => Mage::helper('genmato_composerrepo')->__('Package Name'),
                'note'  => Mage::helper('genmato_composerrepo')->__('Composer package name [vender]/[module-name]'),
                'required' => true,
            )
        );

        $fieldset->addField('status', 'select',
            array(
                'name'  => 'status',
                'label' => Mage::helper('genmato_composerrepo')->__('Status'),
                'title' => Mage::helper('genmato_composerrepo')->__('Status'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('genmato_composerrepo/system_source_packages_status')->toOptionArray()
            )
        );

        $fieldset->addField('description', 'text',
            array(
                'name'  => 'description',
                'label' => Mage::helper('genmato_composerrepo')->__('Package Title'),
                'title' => Mage::helper('genmato_composerrepo')->__('Package Title'),
                'note'  => Mage::helper('genmato_composerrepo')->__('Used to describe package in customer menu'),
                'required' => true,
            )
        );

        $fieldset->addField('product_id', 'text',
            array(
                'name'  => 'product_id',
                'label' => Mage::helper('genmato_composerrepo')->__('Magento Product ID'),
                'title' => Mage::helper('genmato_composerrepo')->__('Magento Product ID'),
                'required' => true,
            )
        );

        $fieldset->addField('repository_url', 'text',
            array(
                'name'  => 'repository_url',
                'label' => Mage::helper('genmato_composerrepo')->__('Repository URL'),
                'title' => Mage::helper('genmato_composerrepo')->__('Repository URL'),
                'required' => true,
            )
        );

        $fieldset->addField('repository_options', 'textarea',
            array(
                'name'  => 'repository_options',
                'label' => Mage::helper('genmato_composerrepo')->__('Repository options'),
                'title' => Mage::helper('genmato_composerrepo')->__('Repository options'),
                'note'  => Mage::helper('genmato_composerrepo')->__('Repository extra parameters in JSON format'),
            )
        );


        $form->addValues($package->getData());

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save', array('id'=>$package->getId())));
        $form->setMethod('post');
        $this->setForm($form);
    }
}