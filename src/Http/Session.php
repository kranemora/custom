<?php
namespace Custom\Http;

/**
 * This class is a wrapper for the native PHP session functions. It provides
 * several defaults for the most common session configuration
 * via external handlers and helps with using session in cli without any warnings.
 *
 * Sessions can be created from the defaults using `Session::create()` or you can get
 * an instance of a new session by just instantiating this class and passing the complete
 * options you want to use.
 *
 * When specific options are omitted, this class will take its defaults from the configuration
 * values from the `session.*` directives in php.ini. This class will also alter such
 * directives when configuration values are provided.
 */
class Session extends \Cake\Http\Session
{
    /**
     * Indica si la sesión se ha cerrado por haberse alcanzado el tiempo de expiración
     *
     * @var bool
     */
    protected $_timeoutExpired = false;
    /**
     * Starts the Session.
     *
     * @return bool True if session was started
     * @throws \RuntimeException if the session was already started
     */
    
    public function start()
    {
        if ($this->_started) {
            return true;
        }

        if ($this->_isCLI) {
            $_SESSION = [];
            $this->id('cli');

            return $this->_started = true;
        }

        if (session_status() === \PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Session was already started');
        }

        if (ini_get('session.use_cookies') && headers_sent($file, $line)) {
            return false;
        }

        if (!session_start()) {
            throw new RuntimeException('Could not start the session');
        }

        $this->_started = true;

        if ($this->_timedOut()) {
            $this->destroy();
			// Se indica que el tiempo de expiración ha sido alcanzado
            $this->_timeoutExpired = true;

            return $this->start();
        }

        return $this->_started;
    }

    /**
     * Verifica si la sesión se ha cerrado por haberse alcanzado el tiempo de expiración.
     *
     * @return bool True Si el tiempo de expiración ha sido alcanzado
     */
	public function isTimeoutExpired() 
    {
		return $this->_timeoutExpired;
	}
}