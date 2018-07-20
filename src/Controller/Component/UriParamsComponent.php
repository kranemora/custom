<?php
namespace Custom\Controller\Component;

use Cake\Controller\Component;
use Custom\Utility\Sanitizer;
    
class UriParamsComponent extends Component
{
    public function clean () {
		
        
        $controller = $this->_registry->getController();

        if (!empty($controller->request->getData())) {
            $controller->request = $controller->request->withParsedBody(Sanitizer::clean($controller->request->getData()));
        }
        if (!empty($controller->request->getQueryParams())) {
			$controller->request = $controller->request->withQueryParams(Sanitizer::clean($controller->request->getQueryParams()));
		}
        if (!empty($controller->request->getParam('pass'))) {
			$controller->request = $controller->request->withParam('pass', Sanitizer::clean($controller->request->getParam('pass')));
		}
    }
}