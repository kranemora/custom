<?php
namespace Custom\ORM;

use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Custom\ORM\Association\BelongsToMany;

/**
 * Database Prefix Trait for CakePHP 3.0
 *
 * @author Florian Krämer
 * @license MIT
 */
trait PrefixSuffixTableTrait {
	/**
	 * The table prefix to use.
	 *
	 * @var string
	 */
	protected $_tablePrefix = '';
	protected $_tableSuffix = '';
    protected $_allowPrefix = true;
    protected $_allowSuffix = false;
    
    public function initialize(array $config)
    {
        $conn = ($config['connection']->config());
        if (strpos($config['registryAlias'], '.')) {
            list($plugin) = pluginSplit($config['registryAlias'], false);
            if (Configure::check($plugin.'.Datasources.'.$conn['name'].'.prefix')) {
                $this->setPrefix(Configure::read($plugin.'.Datasources.'.$conn['name'].'.prefix'));
            } elseif (Configure::check('App.Datasources.'.$conn['name'].'.prefix')) {
                $this->setPrefix(Configure::read('App.Datasources.'.$conn['name'].'.prefix'));
            } elseif (array_key_exists('prefix', $conn)){
                $this->setPrefix($conn['prefix']);
            }
            if (Configure::check($plugin.'.Datasources.'.$conn['name'].'.suffix')) {
                $this->setSuffix(Configure::read($plugin.'.Datasources.'.$conn['name'].'.suffix'));
            } elseif (Configure::check('App.Datasources.'.$conn['name'].'.suffix')) {
                $this->setSuffix(Configure::read('App.Datasources.'.$conn['name'].'.suffix'));
            } elseif (array_key_exists('suffix', $conn)){
                $this->setSuffix($conn['suffix']);
            }
        } 
		else {
            if (Configure::check('App.Datasources.'.$conn['name'].'.prefix')) {
                $this->setPrefix(Configure::read('App.Datasources.'.$conn['name'].'.prefix'));
            } elseif (array_key_exists('prefix', $conn)){
                $this->setPrefix($conn['prefix']);
            }
            if (Configure::check('App.Datasources.'.$conn['name'].'.suffix')) {
                $this->setSuffix(Configure::read('App.Datasources.'.$conn['name'].'.suffix'));
            } elseif (array_key_exists('suffix', $conn)){
                $this->setSuffix($conn['suffix']);
            }
		}

    }

    public function setAllowPrefix($value) 
    {
        $this->_allowPrefix = $value;
    }
    
    public function getAllowPrefix() 
    {
        return $this->_allowPrefix;
    }
    
    public function setAllowSuffix($value) 
    {
        $this->_allowSuffix = $value;
    }
    
    public function getAllowSuffix() 
    {
        return $this->_allowSuffix;
    }
    
    public function setPrefix($prefix)
	{
		if ($this->getAllowPrefix()) {
            $this->_tablePrefix = $prefix;
            $this->setTable($this->getCleanTable('all'));
        }
	}
	
	public function getPrefix()
	{
		return $this->_tablePrefix;
	}
	
    public function setSuffix($suffix)
	{
        if ($this->getAllowSuffix()) {
            $this->_tableSuffix = $suffix;
            $this->setTable($this->getCleanTable('all'));
        }
    }
	
	public function getSuffix()
	{
		return $this->_tableSuffix;
	}
	
	public function setTable($table)
    {
		$this->_table = $this->getPrefix() . $table . $this->getSuffix();
        return $this;
    }
	
    public function getTable()
    {
        if ($this->_table === null) {
            $table = namespaceSplit(get_class($this));
            $table = substr(end($table), 0, -5);
            if (!$table) {
                $table = $this->getAlias();
            }
            $this->_table = $this->getPrefix() . Inflector::underscore($table) . $this->getSuffix();
        }

        return $this->_table;
    }

    public function getCleanTable($clean = 'all') {
        switch ($clean) {
            case 'prefix':
                return str_replace($this->getPrefix(), '', $this->getTable());
            break;
            case 'suffix':
                return str_replace($this->getSuffix(), '', $this->getTable());
            break;
            case 'all':
                return str_replace(array($this->getPrefix(), $this->getSuffix()), array('', ''), $this->getTable());
            break;
        }
    }
    
    public function belongsToMany($associated, array $options = [])
    {
        $options += ['sourceTable' => $this];
        $association = new BelongsToMany($associated, $options);

        return $this->_associations->add($association->getName(), $association);
    }
}