<?php
namespace Custom\ORM;

use Cake\Utility\Text;
use InvalidArgumentException;
use Custom\Exception\OptimisticLockException;
/**
 * Trait OptimisticLockTableTrait
 *
 * @package Extended\ORM
 * @author Hirotomo Kai. Acondicionado por Fernando Pita
 * @link https://github.com/kaihiro/optimistic-lock
 */
trait OptimisticLockTableTrait
{
    protected function _insert($entity, $data)
    {
        if ($this->hasField('lockhash')) {
            $lockhash = str_replace('-', '', Text::uuid());
			$entity->set('lockhash', $lockhash);
            $data['lockhash'] = $lockhash;
        }
        return parent::_insert($entity, $data);
	}

    /**
     * Auxiliary function to handle the update of an entity's data in the table
     *
     * @param \Cake\Datasource\EntityInterface $entity the subject entity from were $data was extracted
     * @param array $data The actual data that needs to be saved
     * @return \Cake\Datasource\EntityInterface|bool
     * @throws \InvalidArgumentException When primary key data is missing.
     */
    protected function _update($entity, $data)
    {
        $primaryColumns = (array)$this->getPrimaryKey();
        $primaryKey = $entity->extract($primaryColumns);

        $data = array_diff_key($data, $primaryKey);
        if (empty($data)) {
            return $entity;
        }

        if (count($primaryColumns) === 0) {
            $entityClass = get_class($entity);
            $table = $this->getTable();
            $message = "Cannot update `$entityClass`. The `$table` has no primary key.";
            throw new InvalidArgumentException($message);
        }

        if (!$entity->has($primaryColumns)) {
            $message = 'All primary key value(s) are needed for updating, ';
            $message .= get_class($entity) . ' is missing ' . implode(', ', $primaryColumns);
            throw new InvalidArgumentException($message);
        }

        // for optimistic lock
        $conditions = $primaryKey;
        if ($this->hasField('lockhash')) {
            $lockhash = str_replace('-', '', Text::uuid());
			if ($entity->has('lockhash')) {
                $conditions['lockhash'] = $entity->get('lockhash');
				$entity->set('lockhash', $lockhash);
                $data['lockhash'] = $lockhash;
            } else {
                $entity->set('lockhash', $lockhash);
                $data['lockhash'] = $lockhash;
            }
        }
        
        $query = $this->query();
        $statement = $query->update()
            ->set($data)
            //->where($primaryKey) For optimistic lock
            ->where($conditions)
            ->execute();

        $success = false;

        if ($statement->errorCode() === '00000') {
            // for optimistic lock
            if ($statement->count() === 0) {
                throw new OptimisticLockException();
            }
            $success = $entity;
        }
        $statement->closeCursor();

        return $success;
    }
    
}