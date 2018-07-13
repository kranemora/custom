<?php
namespace Custom\Exception;
use Cake\Core\Exception\Exception;
/**
 * OptimisticLockException.
 */
class OptimisticLockException extends Exception
{
    public function __construct($message = null, $code = 500, $previous = null)
    {
        if (empty($message)) {
            $message = __('Optimistic lock error.');
        }
        parent::__construct($message, $code, $previous);
    }
}