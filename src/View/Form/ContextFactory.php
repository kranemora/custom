<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.5.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Custom\View\Form;

use Cake\Collection\Collection;
use Cake\Datasource\EntityInterface;
use RuntimeException;
use Cake\View\Form\ArrayContext;
use Cake\View\Form\FormContext;

/**
 * Factory for getting form context instance based on provided data.
 */
class ContextFactory extends \Cake\View\Form\ContextFactory
{
    public static function createWithDefaults(array $providers = [])
    {
        $providers = [
            [
                'type' => 'orm',
                'callable' => function ($request, $data) {
                    if (is_array($data['entity']) || $data['entity'] instanceof Traversable) {
                        $pass = (new Collection($data['entity']))->first() !== null;
                        if ($pass) {
                            return new EntityContext($request, $data);
                        }
                    }
                    if ($data['entity'] instanceof EntityInterface) {
                        return new EntityContext($request, $data);
                    }
                    if (is_array($data['entity']) && empty($data['entity']['schema'])) {
                        return new EntityContext($request, $data);
                    }
                }
            ],
            [
                'type' => 'array',
                'callable' => function ($request, $data) {
                    if (is_array($data['entity']) && isset($data['entity']['schema'])) {
                        return new ArrayContext($request, $data['entity']);
                    }
                }
            ],
            [
                'type' => 'form',
                'callable' => function ($request, $data) {
                    if ($data['entity'] instanceof Form) {
                        return new FormContext($request, $data);
                    }
                }
            ],
        ] + $providers;

        return new static($providers);
    }
}
