<?php
namespace Custom\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * UriParams helper
 */
class UriParamsHelper extends Helper
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
		'defaultAction' => 'index',
        'path' => ':plugin.:prefix.:controller.:action',
		'data' => array()
	];
	
	function get($key = NULL) {
        if (empty($key)) {
            $uri = $this->_normalize([
                'controller' => $this->request->getParam('controller'),
                'action' => $this->getConfig('defaultAction'),
                'plugin' => $this->request->getParam('plugin'),
                'prefix' => $this->request->getParam('prefix')
            ]);
            $key = trim(str_replace(
                [':controller', ':action', ':plugin', ':prefix'],
                [$uri['controller'], $uri['action'], $uri['plugin'], $uri['prefix']],
                $this->getConfig('path')
            ), '.');
		}
		
		return (array) Hash::get($this->getConfig('data'), $key);
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
        
        return $url;
    }
}
