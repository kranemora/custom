<?php
namespace Custom\Storage;

/**
 * Memory based non-persistent storage for authenticated user record.
 */
class MemoryStorage extends \Cake\Auth\Storage\MemoryStorage implements StorageInterface
{
	public function isTimeoutExpired() 
    {
		return false;
	}
}