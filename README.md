# Custom plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

Incluir en el archivo composer.json del proyecto

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/kranemora/custom.git"
    }
],
```

The recommended way to install composer packages is:

```
composer require cakephp-extended/custom
```

## Mensaje de sesión expirada

En la línea 34 del archivo webroot/index.php

```
cambie: use Cake\Http\Server;

por: use Custom\Http\Server;
```

```
Utilice: $this->loadComponent('Custom.Auth');

En vez de: $this->loadComponent('Auth');
```

```
Si necesita personalizar la configuración del componente Auth agregue el storage de Custom.

Por ejemplo: 'storage' => 'Custom.Session'
```

## Actualizar la información del usuario almacenada en la sesión con cada acción

```
Utilize $this->Auth->refreshUser(); en el método initialize de AppController.
```

```
Si va a autenticar por formulario, establezca 'Custom.Form' en la opción 'authenticate'

$this->loadComponent('Custom.Auth', [
    'authenticate' => [
    	'Custom.Form'
    ]
]);
```

## Priorización de los Helpers de Custom en Lazy Load

En src/View/AppView.php

```
cambie: use Cake\View\View;

por: use Custom\View\View;
```

## Optimistic Lock

```
Asegúrese de que no se estén generando tablas automáticamente (Auto-Tables)
```

En los archivos de tablas

```
cambie: use Cake\ORM\Table;

por: use Custom\ORM\Table;
```

## Paginate coherencia en el comportamiento cuando se exceden los límites

En el archivo AppController.php de la aplicación

```
cambie: use Cake\Controller\Controller;

por: use Custom\Controller\Controller;
```

## Prefix y Suffix en tables

```
Asegúrese de que no se estén generando tablas automáticamente (Auto-Tables)
```

En los archivos de tablas

```
cambie: use Cake\ORM\Table;

por: use Custom\ORM\Table;
```

En el archivo de configuración app.php

```
incluir

    'Datasources' => [
        'default' => [
            ...
            'allowPrefix' => true,
            'allowSuffix' => false,
            'prefix' => 'accounts_',
            'suffix' => '_2018'
            ...
        ],
    ],

según corresponda
```

En el archivo de configuración bootstrap.php del plugin

```
incluir 

if (!Configure::check('Accounts.Datasources.default.allowPrefix')) {
    Configure::write('Accounts.Datasources.default.allowPrefix', true);
}
if (!Configure::check('Accounts.Datasources.default.allowSufffix')) {
    Configure::write('Accounts.Datasources.default.allowSuffix', false);
}
if (!Configure::check('Accounts.Datasources.default.prefix')) {
    Configure::write('Accounts.Datasources.default.prefix', 'accounts_');
}
if (!Configure::check('Accounts.Datasources.default.suffix')) {
    Configure::write('Accounts.Datasources.default.suffix', '_2018');
}

según corresponda
```

Por defecto se permite a todas las tablas prefijo, pero no el sufijo. Para habilitar o deshabilitar estas propiedades modifique los archivos Table de los modelos

```
incluir 

$this->setAllowSuffix(true);

o

$this->setAllowPrefix(false);

en el método initialize() antes que la llamada a su antecesor parent::initialize($config)
```

## Inclusión de base de nombre de base de datos en las consultas

```
Asegúrese de que no se estén generando tablas automáticamente (Auto-Tables)
```

En los archivos de tablas

```
cambie: use Cake\ORM\Table;

por: use Custom\ORM\Table;
```

En el archivo de configuración app.php

```
incluir

    'Datasources' => [
        'default' => [
            ...
            'useOwner' => true,
            ...
        ],
    ],
```

En el archivo de configuración bootstrap.php del plugin

```
incluir 

if (!Configure::check('Accounts.Datasources.default.useOwner')) {
    Configure::write('Accounts.Datasources.default.useOwner', true);
}
```
