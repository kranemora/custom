<?php
namespace Custom\View\Helper;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Form\NullContext;

trait OptimisticLockFormHelperTrait
{
    /**
     * Returns an HTML form element.
     *
     * ### Options:
     *
     * - `type` Form method defaults to autodetecting based on the form context. If
     *   the form context's isCreate() method returns false, a PUT request will be done.
     * - `method` Set the form's method attribute explicitly.
     * - `action` The controller action the form submits to, (optional). Use this option if you
     *   don't need to change the controller from the current request's controller. Deprecated since 3.2, use `url`.
     * - `url` The URL the form submits to. Can be a string or a URL array. If you use 'url'
     *    you should leave 'action' undefined.
     * - `encoding` Set the accept-charset encoding for the form. Defaults to `Configure::read('App.encoding')`
     * - `enctype` Set the form encoding explicitly. By default `type => file` will set `enctype`
     *   to `multipart/form-data`.
     * - `templates` The templates you want to use for this form. Any templates will be merged on top of
     *   the already loaded templates. This option can either be a filename in /config that contains
     *   the templates you want to load, or an array of templates to use.
     * - `context` Additional options for the context class. For example the EntityContext accepts a 'table'
     *   option that allows you to set the specific Table class the form should be based on.
     * - `idPrefix` Prefix for generated ID attributes.
     * - `valueSources` The sources that values should be read from. See FormHelper::setValueSources()
     * - `templateVars` Provide template variables for the formStart template.
     *
     * @param mixed $context The context for which the form is being defined.
     *   Can be a ContextInterface instance, ORM entity, ORM resultset, or an
     *   array of meta data. You can use false or null to make a context-less form.
     * @param array $options An array of html attributes and options.
     * @return string An formatted opening FORM tag.
     * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#Cake\View\Helper\FormHelper::create
     */
    public function create($context = null, array $options = [])
    {
        $append = '';

        if ($context instanceof ContextInterface) {
            $this->context($context);
        } else {
            if (empty($options['context'])) {
                $options['context'] = [];
            }
            $options['context']['entity'] = $context;
            $context = $this->_getContext($options['context']);
            unset($options['context']);
        }
		
        $isCreate = $context->isCreate();

        $options += [
            'type' => $isCreate ? 'post' : 'put',
            'action' => null,
            'url' => null,
            'encoding' => strtolower(Configure::read('App.encoding')),
            'templates' => null,
            'idPrefix' => null,
            'valueSources' => null,
        ];

        if (isset($options['action'])) {
            trigger_error('Using key `action` is deprecated, use `url` directly instead.', E_USER_DEPRECATED);
        }

        if (isset($options['valueSources'])) {
            $this->setValueSources($options['valueSources']);
            unset($options['valueSources']);
        }

        if ($options['idPrefix'] !== null) {
            $this->_idPrefix = $options['idPrefix'];
        }
        $templater = $this->templater();

        if (!empty($options['templates'])) {
            $templater->push();
            $method = is_string($options['templates']) ? 'load' : 'add';
            $templater->{$method}($options['templates']);
        }
        unset($options['templates']);

        if ($options['action'] === false || $options['url'] === false) {
            $url = $this->request->getRequestTarget();
            $action = null;
        } else {
            $url = $this->_formUrl($context, $options);
            $action = $this->Url->build($url);
        }

        $this->_lastAction($url);
        unset($options['url'], $options['action'], $options['idPrefix']);

        $htmlAttributes = [];
        switch (strtolower($options['type'])) {
            case 'get':
                $htmlAttributes['method'] = 'get';
                break;
            // Set enctype for form
            case 'file':
                $htmlAttributes['enctype'] = 'multipart/form-data';
                $options['type'] = $isCreate ? 'post' : 'put';
            // Move on
            case 'post':
            // Move on
            case 'put':
            // Move on
            case 'delete':
            // Set patch method
            case 'patch':
                $append .= $this->hidden('_method', [
                    'name' => '_method',
                    'value' => strtoupper($options['type']),
                    'secure' => static::SECURE_SKIP
                ]);
            // Default to post method
            default:
                $htmlAttributes['method'] = 'post';
        }
        if (isset($options['method'])) {
            $htmlAttributes['method'] = strtolower($options['method']);
        }
        if (isset($options['enctype'])) {
            $htmlAttributes['enctype'] = strtolower($options['enctype']);
        }

        $this->requestType = strtolower($options['type']);

        if (!empty($options['encoding'])) {
            $htmlAttributes['accept-charset'] = $options['encoding'];
        }
        unset($options['type'], $options['encoding']);

        $htmlAttributes += $options;

        $this->fields = [];
        if ($this->requestType !== 'get') {
            $append .= $this->_csrfField();
			// Hack from extended. Start. For optimistic lock. Based on kaihiro's work https://github.com/kaihiro/optimistic-lock
			$append .= $this->_lockhashField();
			// Hack from extended. End. For optimistic lock. Based on kaihiro's work https://github.com/kaihiro/optimistic-lock
        }

        if (!empty($append)) {
            $append = $templater->format('hiddenBlock', ['content' => $append]);
        }

        $actionAttr = $templater->formatAttributes(['action' => $action, 'escape' => false]);

        return $this->formatTemplate('formStart', [
            'attrs' => $templater->formatAttributes($htmlAttributes) . $actionAttr,
            'templateVars' => isset($options['templateVars']) ? $options['templateVars'] : []
        ]) . $append;
    }
	
	// Hack from extended. Start. For optimistic lock. Based on kaihiro's work https://github.com/kaihiro/optimistic-lock
	protected function _lockhashField()
	{
        $out = '';
		if ($this->_context instanceof NullContext) {
			return $out;
		}

		$data = Hash::flatten($this->_context->entity()->toArray());
		foreach ($data as $k => $v) 
		{
			if (strpos($k, 'lockhash') !== false)
			{
				$out .= $this->hidden($k);
			}
		}
		return $out;
	}
}