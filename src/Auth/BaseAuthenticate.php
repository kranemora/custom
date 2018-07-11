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
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Custom\Auth;

use Cake\ORM\TableRegistry;

abstract class BaseAuthenticate extends \Cake\Auth\BaseAuthenticate
{
    /**
     * Find a user record using identifier provided.
     *
     * @param string $id The identifier.
     * @return bool|array Either false on failure, or an array of user data.
     */
    protected function _findUserById($id)
    {
        $result = $this->_queryUserById($id)->first();

        if (empty($result)) {
            return false;
        }

        return $result->toArray();
	}

    /**
     * Get query object for fetching user from database.
     *
     * @param string $id The identifier.
     * @return \Cake\ORM\Query
     */
    protected function _queryUserById($id)
	{
        $config = $this->_config;
        $table = TableRegistry::get($config['userModel']);

        $options = [
            'conditions' => [$table->aliasField('id') => $id]
        ];
        if (!empty($config['scope'])) {
            $options['conditions'] = array_merge($options['conditions'], $config['scope']);
        }
        if (!empty($config['contain'])) {
            $options['contain'] = $config['contain'];
        }

        $finder = $config['finder'];
        if (is_array($finder)) {
            $options += current($finder);
            $finder = key($finder);
        }

        if (!isset($options['id'])) {
            $options['id'] = $id;
        }

        return $table->find($finder, $options);
	}

    /**
     * Recupera la información del usuario almacenada en la sesión desde la base de datos.
     *
     * @param $id Identifier.
     * @return bool|array Either false on failure, or an array of user data.
     */
	public function freshUser($id) {
        
		if (empty($id) && !is_numeric($id)) {
			return false;
		}
		return $this->_findUserById(
            $id
        );
	}
}
