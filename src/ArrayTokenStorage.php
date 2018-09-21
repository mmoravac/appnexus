<?php
namespace MMoravac\AppNexusClient;

/**
 * ArrayTokenStorage
 *
 * @uses TokenStorage
 * @author Alexey Karapetov <karapetov@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 */
class ArrayTokenStorage implements TokenStorage
{
    private $storage = array();

    /**
     * set token
     *
     * @param string $username
     * @param string $token
     * @return void
     */
    public function set($username, $token)
    {
        $this->storage[$username] = $token;
    }

    /**
     * get token
     *
     * @param string $username
     * @return string|false
     */
    public function get($username)
    {
        return isset($this->storage[$username]) ? $this->storage[$username] : false;
    }
}
