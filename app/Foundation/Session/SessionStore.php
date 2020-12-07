<?php declare(strict_types = 1);

namespace App\Foundation\Session;

use SessionHandlerInterface;
use Throwable;

class SessionStore implements SessionHandlerInterface
{
    public function __construct()
    {
        $this->open(null, null);
        $this->setToken();
    }

    public function close()
    {
        session_write_close();
    }

    public function destroy($session_id)
    {
        $this->close();
        $this->gc(0);
        session_destroy();
    }

    public function gc($maxlifetime)
    {
        session_unset();
    }

    public function open($save_path, $name)
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }
        session_name();
        session_start();
    }

    public function read($session_id)
    {
        if (!isset($_SESSION[$session_id])) {
            return null;
        }

        return $_SESSION[$session_id];
    }

    public function write($session_id, $session_data)
    {
        $_SESSION[$session_id] = $session_data;
    }

    public function setToken()
    {
        if ($this->read('_token')) {
            return;
        }
        $this->write('_token', bin2hex(random_bytes(32)));
    }

    public function token()
    {
        return $this->read('_token');
    }
}
