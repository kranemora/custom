<?php
namespace Custom\ORM;

use Cake\ORM\Entity;
use Cake\ORM\Rule\ExistsIn;
use Cake\ORM\Rule\ValidCount;

trait ValidateTableTrait {

    public function validateExistsIn($value, array $options, array $context = null)
    {

        if ($context === null) {
            $context = $options;
        }
        $entity = new Entity(
            $context['data'],
            [
                'useSetters' => false,
                'markNew' => $context['newRecord'],
                'source' => $this->getRegistryAlias()
            ]
        );
        $fields = array_merge(
            [$context['field']],
            isset($options['scope']) ? (array)$options['scope'] : []
        );
        $values = $entity->extract($fields);
        foreach ($values as $field) {
            if ($field !== null && !is_scalar($field)) {
                return false;
            }
        }
        $class = ExistsIn::class;
        $rule = new $class($fields, $options['table'], $options);

        return $rule($entity, ['repository' => $this]);
    }

    public function validateValidCount($value, array $options, array $context = null)
    {
        if ($context === null) {
            $context = $options;
        }
        
        if (isset($context['data'][$context['field']]['_ids'])) {
            $context['data'][$context['field']] = $context['data'][$context['field']]['_ids'];
        }
        
        $entity = new Entity(
            $context['data'],
            [
                'useSetters' => false,
                'markNew' => $context['newRecord'],
                'source' => $this->getRegistryAlias()
            ]
        );
        $field = $context['field'];

        $class = ValidCount::class;
        $rule = new $class($field);

        return $rule($entity, $options);
    }

}