<?php
namespace Custom\Controller\Component;

use Cake\Controller\Component;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Search component
 */
class SearchComponent extends Component
{
    private $condition = [];
    private $fieldList = [];
    private $criteria = [];
    
    public function process($fields = []) {
        $controller = $this->_registry->getController();
		
		$this->setFieldList(Hash::merge($this->fieldList, $fields));
        
        if ($controller->request->is('POST')) {
			$this->setCriteria(Hash::merge($controller->request->getQuery(), $controller->request->getData()));
            return $this->redirect();
		}
        $this->setCriteria($controller->request->getQuery());
        $this->setData();
		$this->buildCondition();
        
        return $this->condition;
    }

	public function redirect() {
        $controller = $this->_registry->getController();
        
        return $this->_registry->getController()->redirect(Router::reverseToArray($controller->request->withQueryParams($this->criteria)));
	}
    
    private function setCriteria ($criteria) {
        $fields = [];
        foreach ($this->fieldList as $k => $v) {
           if ($v['type'] != 'virtual') {
               $fields[$k] = $this->fieldList[$k];
           }
        }
        $criteriaCleaned = array_filter(Hash::flatten($criteria), function ($v, $k) use ($fields) {
            return array_key_exists($k, $fields) && $v != '';	
		}, ARRAY_FILTER_USE_BOTH);

        $this->criteria = Hash::expand($criteriaCleaned);
    }
    
    public function setData() {
        $controller = $this->_registry->getController();
        $controller->request = $controller->request->withParsedBody($this->criteria);
    }
    
    public function buildCondition() {
        
        $criteria = Hash::flatten($this->criteria);
        foreach ($this->fieldList as $k => $v) {
            if($v['type'] != 'virtual' && !array_key_exists($k, $criteria)) {
                continue;
            }
            $field = array_reverse(explode('.', $k));
            if (count($field)>1) {
                $field = $field[1].'.'.$field[0];
            } else {
                $field = $field[0];
            }

            switch ($v['type']) {
                case 'value':
                    $this->condition[$field] = $criteria[$k];
                break;
                case 'like':
                    $this->condition[$field.' LIKE'] = '%'.trim(str_replace('*', '%', $criteria[$k]),'%').'%';
                break;
                case 'virtual':
                    $v['params'] = !empty($v['params'])?$v['params']:[];
                    $this->condition = Hash::merge($this->condition, call_user_func([$v['function'][0], $v['function'][1]], $criteria, $v['params']));
                break;
                case 'auto':
                default:
                    if (strpos($criteria[$k], '_') !== false || strpos($criteria[$k], '*') !== false || strpos($criteria[$k], '%') !== false) {
                        $this->condition[$field.' LIKE'] = str_replace('*', '%', $criteria[$k]);
                    } else {
                        $this->condition[$field] = $criteria[$k];
                    }
                break;
            }
        }
    }
    
    public function setFieldList ($fields = []){
        $fields = Hash::normalize($fields);
        foreach ($fields as $k => $v) {
            if (empty($v)) {
                $fields[$k] = [
                    'type' => 'auto'
                ];
            }
        }
        $this->fieldList = $fields;
    }

}