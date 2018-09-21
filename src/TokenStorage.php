<?php
namespace MMoravac\AppNexusClient;

interface TokenStorage
{
	/**
	 * set
	 *
	 * @param string $username
	 * @param string $token
	 * @return void
	 */
	public function set($username, $token);

	/**
	 * get token for given username
	 *
	 * @param string $username
	 * @return string|false
	 */
	public function get($username);
}
