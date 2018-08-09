<?php
namespace Custom\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Routing\Router;
use Custom\Utility\Sanitizer;
use Cake\Event\Event;

class UriParamsComponent extends Component
{
    /**
     * The Session object instance
     *
     * @var \Cake\Http\Session
     */
    protected $_session;
    
    /**
     * The Controller object instance
     *
     * @var \Cake\Controller\Component
     */
    protected $_controller;
    
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
		'defaultAction' => 'index',
		'sessionKey' => 'UriParams',
        'path' => ':plugin.:prefix.:controller.:action'
	];

    /**
     * Constructor
     *
     * @param \Cake\Controller\ComponentRegistry $registry A ComponentRegistry for this component
     * @param array $config Array of config.
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->_controller = $registry->getController();
        $this->_session = $this->_controller->request->getSession();
    }

    public function clean () {
		
        if (!empty($this->_controller->request->getData())) {
            $this->_controller->request = $this->_controller->request->withParsedBody(Sanitizer::clean($this->_controller->request->getData()));
        }
        if (!empty($this->_controller->request->getQueryParams())) {
			$this->_controller->request = $this->_controller->request->withQueryParams(Sanitizer::clean($this->_controller->request->getQueryParams()));
		}
        if (!empty($this->_controller->request->getParam('pass'))) {
			$this->_controller->request = $this->_controller->request->withParam('pass', Sanitizer::clean($this->_controller->request->getParam('pass')));
		}
    }

	public function set($params = [], $key = NULL) {
        
        $uri = $this->_normalize(Router::parseRequest($this->_controller->request));
        
        $params = Hash::merge(['pass' => $uri['pass'], '?' => $uri['?']], $params);

        $key = trim(str_replace(
            [':controller', ':action', ':plugin', ':prefix'],
            [$uri['controller'], $uri['action'], $uri['plugin'], $uri['prefix']],
            $this->getConfig('path')
        ), '.');
        
		if ($this->_inAuth($uri['action'])) { 
			$auth = Hash::remove($this->_controller->Auth->storage()->read(), Inflector::underscore($this->getConfig('sessionKey')).'.'.$key);
			
            $auth = Hash::merge(
				$auth, 
				[
					$this->getConfig('sessionKey') => Hash::expand([$key => $params])
				]
			);
            $this->_controller->Auth->storage()->write($auth);
        } else {
            $this->_session->write($this->getConfig('sessionKey').'.' . $key, $params);
        }
        
    }

	public function get ($key = NULL) {
        if ($key == 'all') {
            $auth = (array) $this->_controller->Auth->storage()->read();
			return Hash::merge((array) Hash::get($auth, $this->getConfig('sessionKey')), (array) $this->_session->read($this->getConfig('sessionKey')));
		} 
        else if (empty($key)) {
            $uri = $this->_normalize([
                'controller' => $this->_controller->request->getParam('controller'),
                'action' => $this->getConfig('defaultAction'),
                'plugin' => $this->_controller->request->getParam('plugin'),
                'prefix' => $this->_controller->request->getParam('prefix')
            ]);
            $key = trim(str_replace(
                [':controller', ':action', ':plugin', ':prefix'],
                [$uri['controller'], $uri['action'], $uri['plugin'], $uri['prefix']],
                $this->getConfig('path')
            ), '.');
		}
        $keys = explode('.', $key);
        if ($this->_inAuth($keys[count($keys)-1])) { 
			$auth = $this->_controller->Auth->storage()->read();
			return Hash::get($auth, $this->getConfig('sessionKey').'.'.$key);
		} 
        else {
			return $this->_session->read($this->getConfig('sessionKey').'.'.$key);
		}
    }
    
    /**
     * Helper method for dasherizing keys in a URL array.
     *
     * @param array $url An array of URL keys.
     * @return array
     */
    protected function _normalize($url)
    {
        foreach (['prefix', 'controller', 'plugin', 'action'] as $element) {
            if (!empty($url[$element])) {
                $url[$element] = Inflector::dasherize($url[$element]);
            } else {
                $url[$element] = null;
            }
        }
        
        foreach (['pass', '?'] as $element) {
            if (empty($url[$element])) {
                $url[$element] = [];
            }
        }
        return $url;
    }

    /**
     * Helper method para determinar si la acción necesita o no autorización.
     *
     * @param array $action string.
     * @return boolean
     */
	protected function _inAuth($action) {
		if ($this->_controller->Auth instanceof AuthComponent) {

			if (in_array($action, array_map('strtolower', $this->_controller->Auth->allowedActions))) {
				return false;
			}
			
			return true;
		}
		
		return false;
	}
	
	public function beforeRender (Event $event) {
		$event->getSubject()->viewBuilder()->setHelpers(['Custom.UriParams' => ['defaultAction' => $this->getConfig('defaultAction'), 'path' => $this->getConfig('path'), 'data' => $this->get('all')]]);
	}
}