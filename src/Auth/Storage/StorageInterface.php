<?php
namespace Custom\Auth\Storage;

/**
 * Describes the methods that any class representing an Auth data storage should
 * comply with.
 */
interface StorageInterface extends \Cake\Auth\Storage\StorageInterface
{
	public function isTimeoutExpired();
}