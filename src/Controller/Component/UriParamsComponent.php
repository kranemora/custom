<?php
namespace Custom\Controller\Component;

use Cake\Controller\Component;
use Custom\Utility\Sanitizer;
    
class UriParamsComponent extends Component
{
    public function clean () {
		
        
        $controller = $this->_registry->getController();

        if (!empty($controller->request->getData())) {
			$controller->request->data = Sanitizer::clean($controller->request->getData());
        }
        if (!empty($controller->request->getQuery())) {
			$controller->request->query = Sanitizer::clean($controller->request->getQuery());
		}
        if (!empty($controller->request->getParam('pass'))) {
			$controller->request->params['pass'] = Sanitizer::clean($controller->request->getParam('pass'));
		}
    }
}