<?php
namespace Custom\Http;

use Cake\Core\Configure;

use Cake\Http\ServerRequest;
use Custom\Http\Session;

/**
 * Factory for making ServerRequest instances.
 *
 * This subclass adds in CakePHP specific behavior to populate
 * the basePath and webroot attributes. Furthermore the Uri's path
 * is corrected to only contain the 'virtual' path for the request.
 */
abstract class ServerRequestFactory extends \Cake\Http\ServerRequestFactory{
    /**
     * {@inheritDoc}
     */
    public static function fromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null
    ) {
        $server = static::normalizeServer($server ?: $_SERVER);
        $uri = static::createUri($server);
        $sessionConfig = (array)Configure::read('Session') + [
            'defaults' => 'php',
            'cookiePath' => $uri->webroot
        ];
        $session = Session::create($sessionConfig);
        $request = new ServerRequest([
            'environment' => $server,
            'uri' => $uri,
            'files' => $files ?: $_FILES,
            'cookies' => $cookies ?: $_COOKIE,
            'query' => $query ?: $_GET,
            'post' => $body ?: $_POST,
            'webroot' => $uri->webroot,
            'base' => $uri->base,
            'session' => $session,
        ]);

        return $request;
    }
    
}