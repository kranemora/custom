<?php
namespace Custom\ORM\Association;

use Cake\Core\App;
use Cake\Utility\Inflector;

trait PrefixSuffixBelongsToManyTrait {
    
	public function junction($table = null)
    {
        if ($table === null && $this->_junctionTable) {
            return $this->_junctionTable;
        }

        $tableLocator = $this->getTableLocator();
        if ($table === null && $this->_through) {
            $table = $this->_through;
        } elseif ($table === null) {
            $tableName = $this->_junctionTableName();
			$tableAlias = Inflector::camelize(str_replace(array($this->getSource()->getPrefix(), $this->getSource()->getSuffix()), array('', ''), $tableName));

            $config = [];
            if (!$tableLocator->exists($tableAlias)) {
                $config = ['table' => $tableName];

                // Propagate the connection if we'll get an auto-model
                if (!App::className($tableAlias, 'Model/Table', 'Table')) {
                    $config['connection'] = $this->getSource()->getConnection();
                }
            }
            $table = $tableLocator->get($tableAlias, $config);
        }

        if (is_string($table)) {
            $table = $tableLocator->get($table);
        }
        $source = $this->getSource();
        $target = $this->getTarget();

        $this->_generateSourceAssociations($table, $source);
        $this->_generateTargetAssociations($table, $source, $target);
        $this->_generateJunctionAssociations($table, $source, $target);

        return $this->_junctionTable = $table;
    }
	
    /**
     * Sets the name of the junction table.
     * If no arguments are passed the current configured name is returned. A default
     * name based of the associated tables will be generated if none found.
     *
     * @param string|null $name The name of the junction table.
     * @return string
     */
    protected function _junctionTableName($name = null)
    {
		if ($name === null)
		{
            if (empty($this->_junctionTableName))
			{
                $tablesNames = array_map('\Cake\Utility\Inflector::underscore', [
                    str_replace(array($this->getSource()->getPrefix(), $this->getSource()->getSuffix()), array('', ''), $this->getSource()->getTable()),
                    str_replace(array($this->getSource()->getPrefix(), $this->getSource()->getSuffix()), array('', ''), $this->getTarget()->getTable())
                ]);
                sort($tablesNames);
                $this->_junctionTableName = $this->getSource()->getPrefix().implode('_', $tablesNames).$this->getSource()->getSuffix();
            }

            return $this->_junctionTableName;
        } 
		else 
		{
			$this->_junctionTableName = $this->getSource()->getPrefix().$name.$this->getSource()->getSuffix();
        	return $this->_junctionTableName;
		}

    }

    public function getForeignKey()
    {
        if ($this->_foreignKey === null) {
            $this->_foreignKey = $this->_modelKey($this->getSource()->getAlias());
        }

        return $this->_foreignKey;
    }
}