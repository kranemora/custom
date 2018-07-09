<?php
namespace Custom\Auth\Storage;

/**
 * Session based persistent storage for authenticated user record.
 *
 * @mixin \Cake\Core\InstanceConfigTrait
 */
class SessionStorage extends \Cake\Auth\Storage\SessionStorage implements StorageInterface
{
	public function isTimeoutExpired() {
		return $this->_session->isTimeoutExpired();
	}
}
