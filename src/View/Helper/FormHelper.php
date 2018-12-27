<?php
namespace Custom\View\Helper;

use Custom\View\Form\ContextFactory;

class FormHelper extends \Cake\View\Helper\FormHelper
{
	use OptimisticLockFormHelperTrait;

    /**
     * Set the context factory the helper will use.
     *
     * @param \Cake\View\Form\ContextFactory|null $instance The context factory instance to set.
     * @param array $contexts An array of context providers.
     * @return \Cake\View\Form\ContextFactory
     */
    public function contextFactory(\Cake\View\Form\ContextFactory $instance = null, array $contexts = [])
    {
        if ($instance === null) {
            if ($this->_contextFactory === null) {
                $this->_contextFactory = ContextFactory::createWithDefaults($contexts);
            }

            return $this->_contextFactory;
        }
        $this->_contextFactory = $instance;
        return $this->_contextFactory;
    }
}