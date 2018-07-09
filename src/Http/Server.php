<?php
namespace Custom\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

use Cake\Http\Response;
use Cake\Http\MiddlewareQueue;

/**
 * Runs an application invoking all the PSR7 middleware and the registered application.
 */
class Server extends \Cake\Http\Server{
    
    /**
     * Run the request/response through the Application and its middleware.
     *
     * This will invoke the following methods:
     *
     * - App->bootstrap() - Perform any bootstrapping logic for your application here.
     * - App->middleware() - Attach any application middleware here.
     * - Trigger the 'Server.buildMiddleware' event. You can use this to modify the
     *   from event listeners.
     * - Run the middleware queue including the application.
     *
     * @param \Psr\Http\Message\ServerRequestInterface|null $request The request to use or null.
     * @param \Psr\Http\Message\ResponseInterface|null $response The response to use or null.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException When the application does not make a response.
     */
    public function run(ServerRequestInterface $request = null, ResponseInterface $response = null)
    {
        $this->bootstrap();

        $response = $response ?: new Response();
        $request = $request ?: ServerRequestFactory::fromGlobals();

        $middleware = $this->app->middleware(new MiddlewareQueue());
        if ($this->app instanceof PluginApplicationInterface) {
            $middleware = $this->app->pluginMiddleware($middleware);
        }

        if (!($middleware instanceof MiddlewareQueue)) {
            throw new RuntimeException('The application `middleware` method did not return a middleware queue.');
        }
        $this->dispatchEvent('Server.buildMiddleware', ['middleware' => $middleware]);
        $middleware->add($this->app);

        $response = $this->runner->run($middleware, $request, $response);

        if (!($response instanceof ResponseInterface)) {
            throw new RuntimeException(sprintf(
                'Application did not create a response. Got "%s" instead.',
                is_object($response) ? get_class($response) : $response
            ));
        }

        return $response;
    }
}