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
namespace Custom\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Custom\Datasource\Paginator;

/**
 * This component is used to handle automatic model data pagination. The primary way to use this
 * component is to call the paginate() method. There is a convenience wrapper on Controller as well.
 *
 * ### Configuring pagination
 *
 * You configure pagination when calling paginate(). See that method for more details.
 *
 * @link https://book.cakephp.org/3.0/en/controllers/components/pagination.html
 */
class PaginatorComponent extends \Cake\Controller\Component\PaginatorComponent
{

    /**
     * {@inheritDoc}
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        if (isset($config['paginator'])) {
            if (!$config['paginator'] instanceof Paginator) {
                throw new InvalidArgumentException('Paginator must be an instance of ' . Paginator::class);
            }
            $this->_paginator = $config['paginator'];
            unset($config['paginator']);
        } else {
            $this->_paginator = new Paginator();
        }

        Component::__construct($registry, $config);
    }

}
