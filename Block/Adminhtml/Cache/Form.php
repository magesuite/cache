<?php

namespace MageSuite\Cache\Block\Adminhtml\Cache;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();


        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'tag',
            'text',
            [
                'name' => 'tag',
                'label' => __('Tag'),
                'class' => 'txt-type',
                'title' => __('Tag'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('URL'),
                'class' => 'txt-type',
                'title' => __('URL'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'submit',
            'submit',
            [
                'type' => 'submit',
                'name' => 'submit',
                'class' => 'action-default',
                'label' => __(''),
                'value' => 'Flush',
                'title' => __('Flush'),
                'required' => false
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();

    }
}
