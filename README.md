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

Mensaje de sesión expirada

En la línea 34 del archivo webroot/index.php

cambie: use Cake\Http\Server;

por: use Custom\Http\Server;

Utilice: $this->loadComponent('Custom.Auth');

En vez de: $this->loadComponent('Auth');

Si necesita personalizar la configuración del componente Auth agregue el storage de Custom.

Por ejemplo: 'storage' => 'Custom.Session'