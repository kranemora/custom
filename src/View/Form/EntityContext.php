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
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Custom\View\Form;

/**
 * Provides a form context around a single entity and its relations.
 * It also can be used as context around an array or iterator of entities.
 *
 * This class lets FormHelper interface with entities or collections
 * of entities.
 *
 * Important Keys:
 *
 * - `entity` The entity this context is operating on.
 * - `table` Either the ORM\Table instance to fetch schema/validators
 *   from, an array of table instances in the case of a form spanning
 *   multiple entities, or the name(s) of the table.
 *   If this is null the table name(s) will be determined using naming
 *   conventions.
 * - `validator` Either the Validation\Validator to use, or the name of the
 *   validation method to call on the table object. For example 'default'.
 *   Defaults to 'default'. Can be an array of table alias=>validators when
 *   dealing with associated forms.
 */
class EntityContext extends \Cake\View\Form\EntityContext
{
    /**
     * Check if a field should be marked as required.
     *
     * @param string $field The dot separated path to the field you want to check.
     * @return bool
     */
    public function isRequired($field)
    {
        if (substr($field, -5) === '._ids') {
            $field = substr($field, 0, -5);
        }

        $parts = explode('.', $field);
        $entity = $this->entity($parts);

        $isNew = true;
        if ($entity instanceof EntityInterface) {
            $isNew = $entity->isNew();
        }

        $validator = $this->_getValidator($parts);
        $fieldName = array_pop($parts);
        if (!$validator->hasField($fieldName)) {
            return false;
        }
        if ($this->type($field) !== 'boolean') {
            return $validator->isEmptyAllowed($fieldName, $isNew) === false;
        }

        return false;
    }
}
