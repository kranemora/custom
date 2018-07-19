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
 * @since         2.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Custom\View;

use Cake\View\Exception\MissingHelperException;

/**
 * HelperRegistry is used as a registry for loaded helpers and handles loading
 * and constructing helper class objects.
 */
class HelperRegistry extends \Cake\View\HelperRegistry
{
    /**
     * Tries to lazy load a helper based on its name, if it cannot be found
     * in the application folder, then it tries looking under the current plugin
     * if any
     *
     * @param string $helper The helper name to be loaded
     * @return bool whether the helper could be loaded or not
     * @throws \Cake\View\Exception\MissingHelperException When a helper could not be found.
     *    App helpers are searched, and then plugin helpers.
     */
    public function __isset($helper)
    {
        if (isset($this->_loaded[$helper])) {
            return true;
        }

        try {
            $this->load('Custom.'.$helper);
            
            return true;
        } catch (MissingHelperException $customException) {
            try {
                $this->load($helper);
                
                return true;
            } catch (MissingHelperException $exception) {
                if ($this->_View->plugin) {
                    $this->load($this->_View->plugin . '.' . $helper);

                    return true;
                }
            }
        }

        //if (!empty($exception)) {
        //    throw $exception;
        //}

        return true;
    }

}