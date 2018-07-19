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
 * @since         0.10.0
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Custom\View;

/**
 * View, the V in the MVC triad. View interacts with Helpers and view variables passed
 * in from the controller to render the results of the controller action. Often this is HTML,
 * but can also take the form of JSON, XML, PDF's or streaming files.
 *
 * CakePHP uses a two-step-view pattern. This means that the template content is rendered first,
 * and then inserted into the selected layout. This also means you can pass data from the template to the
 * layout using `$this->set()`
 *
 * View class supports using plugins as themes. You can set
 *
 * ```
 * public function beforeRender(\Cake\Event\Event $event)
 * {
 *      $this->viewBuilder()->setTheme('SuperHot');
 * }
 * ```
 *
 * in your Controller to use plugin `SuperHot` as a theme. Eg. If current action
 * is PostsController::index() then View class will look for template file
 * `plugins/SuperHot/Template/Posts/index.ctp`. If a theme template
 * is not found for the current action the default app template file is used.
 *
 * @property \Cake\View\Helper\BreadCrumbsHelper $BreadCrumbs
 * @property \Cake\View\Helper\FlashHelper $Flash
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\NumberHelper $Number
 * @property \Cake\View\Helper\PaginatorHelper $Paginator
 * @property \Cake\View\Helper\RssHelper $Rss
 * @property \Cake\View\Helper\SessionHelper $Session
 * @property \Cake\View\Helper\TextHelper $Text
 * @property \Cake\View\Helper\TimeHelper $Time
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Cake\View\ViewBlock $Blocks
 * @property string $view
 * @property string $viewPath
 */
class View extends \Cake\View\View
{
    /**
     * Get the helper registry in use by this View class.
     *
     * @return \Cake\View\HelperRegistry
     */
    public function helpers()
    {
        if ($this->_helpers === null) {
            $this->_helpers = new HelperRegistry($this);
        }

        return $this->_helpers;
    }
}