<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.5.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Custom\Datasource;

class Paginator extends \Cake\Datasource\Paginator
{

    public function paginate($object, array $params = [], array $settings = [])
    {
		$query = null;
        if ($object instanceof QueryInterface) {
            $query = $object;
            $object = $query->getRepository();
        }

        $alias = $object->getAlias();
        $defaults = $this->getDefaults($alias, $settings);
        $options = $this->mergeOptions($params, $defaults);
        $options = $this->validateSort($object, $options);
        $options = $this->checkLimit($options);

        $options += ['page' => 1, 'scope' => null];
        $options['page'] = (int)$options['page'] < 1 ? 1 : (int)$options['page'];
        list($finder, $options) = $this->_extractFinder($options);

        if (empty($query)) {
            $query = $object->find($finder, $options);
        } else {
            $query->applyOptions($options);
        }
		
        $cleanQuery = clone $query;
        $count = $cleanQuery->count();
        $page = $options['page'];
        $limit = $options['limit'];
        $pageCount = max((int)ceil($count / $limit), 1);
        $requestedPage = $page;
        $page = min($page, $pageCount);
		
        if ($requestedPage > $page) {
			$query->page($page);
		}

        $results = $query->all();
        $numResults = count($results);

        $order = (array)$options['order'];
        $sortDefault = $directionDefault = false;
        if (!empty($defaults['order']) && count($defaults['order']) === 1) {
            $sortDefault = key($defaults['order']);
            $directionDefault = current($defaults['order']);
        }

        $paging = [
            'finder' => $finder,
            'page' => $page,
            'current' => $numResults,
            'count' => $count,
            'perPage' => $limit,
            'prevPage' => $page > 1,
            'nextPage' => $count > ($page * $limit),
            'pageCount' => $pageCount,
            'sort' => key($order),
            'direction' => current($order),
            'limit' => $defaults['limit'] != $limit ? $limit : null,
            'sortDefault' => $sortDefault,
            'directionDefault' => $directionDefault,
            'scope' => $options['scope'],
        ];

        $this->_pagingParams = [$alias => $paging];

        return $results;
    }
}
