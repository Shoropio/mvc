<?php

namespace App\Helpers;

use App\Helpers\Cookie;
use App\Database;
use App\Helpers\Redirect;
use App\Helpers\Password;

class Session {

    // atributos de la clase
    public  $loggedIn;
    public static $authCheck = false;
    public static $authUser = null;
    private $db;
    private $table = 'sessions';
    private $sessionId;
    private $data;

    /**
     * The session cookie name, must contain only [0-9a-z_-] characters.
     *
     * @var string
     */
    private $sessionCookieName = 'shoropio_session';

    /**
     * The number of SECONDS you want the session to last.
     * Setting it to 0 (zero) means expire when the browser is closed.
     *
     * @var int
     */
    private $sessionExpiration = 7200;

    /**
     * The session cookie instance.
     *
     * @var Cookie
     */
    private $cookie;

    /**
     * The domain name to use for cookies.
     * Set to .your-domain.com for site-wide cookies.
     *
     * @var string
     *
     * @deprecated
     */
    private $cookieDomain = '';

    /**
     * Path used for storing cookies.
     * Typically will be a forward slash.
     *
     * @var string
     *
     * @deprecated
     */
    private $cookiePath = '/';

    /**
     * Cookie will only be set if a secure HTTPS connection exists.
     *
     * @var bool
     *
     * @deprecated
     */
    private $cookieSecure = false;

    /**
     * Cookie SameSite setting as described in RFC6265
     * Must be 'None', 'Lax' or 'Strict'.
     *
     * @var string
     *
     * @deprecated
     */
    private $cookieSameSite = Cookie::SAMESITE_LAX;

    public function __construct() {
        // Inicializar la conexión a la base de datos
        $this->db = new Database();
        //Redirect::to('/login');

        //$this->startSession();

        $this->cookie = new Cookie($this->sessionCookieName, '', [
            'expires'  => $this->sessionExpiration === 0 ? 0 : time() + $this->sessionExpiration,
            'path'     => $this->cookiePath,
            'domain'   => $this->cookieDomain,
            'secure'   => $this->cookieSecure,
            'httponly' => true, // for security
            'samesite' => Cookie::SAMESITE_LAX,
            'raw'      => false,
        ]);
    }

    /**
     * Initialize the session container and starts up the session.
     *
     * @return $this|void
     */
    public function start()
    {
        $this->configure();
        // Sanitize the cookie, because apparently PHP doesn't do that for userspace handlers
        if (isset($_COOKIE[$this->sessionCookieName])
            && (! is_string($_COOKIE[$this->sessionCookieName]) || ! preg_match('#\A' . $this->sidRegexp . '\z#', $_COOKIE[$this->sessionCookieName]))
        ) {
            unset($_COOKIE[$this->sessionCookieName]);
        }

        $this->startSession();
    }

    /**
     * Starts the session.
     * Extracted for testing reasons.
     */
    protected function startSession()
    {
        if (ENVIRONMENT === 'testing') {
            $_SESSION = [];

            return;
        }

        session_start(); // @codeCoverageIgnore
    }

    /**
     * Configuration.
     *
     * Handle input binds and configuration defaults.
     */
    protected function configure()
    {
        if (empty($this->sessionCookieName)) {
            $this->sessionCookieName = ini_get('session.name');
        } else {
            ini_set('session.name', $this->sessionCookieName);
        }

        $sameSite = $this->cookie->getSameSite() ?: ucfirst(Cookie::SAMESITE_LAX);

        $params = [
            'lifetime' => $this->sessionExpiration,
            'path'     => $this->cookie->getPath(),
            'domain'   => $this->cookie->getDomain(),
            'secure'   => $this->cookie->isSecure(),
            'httponly' => true, // HTTP only; Yes, this is intentional and not configurable for security reasons.
            'samesite' => $sameSite,
        ];

        ini_set('session.cookie_samesite', $sameSite);
        session_set_cookie_params($params);

        if (!isset($this->sessionExpiration)) {
            $this->sessionExpiration = (int) ini_get('session.gc_maxlifetime');
        } elseif ($this->sessionExpiration > 0) {
            ini_set('session.gc_maxlifetime', (string) $this->sessionExpiration);
        }

        if (!empty($this->sessionSavePath)) {
            ini_set('session.save_path', $this->sessionSavePath);
        }

        // Security is king
        ini_set('session.use_trans_sid', '0');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');

        $this->configureSidLength();
    }

    /**
     * Configure session ID length
     *
     * To make life easier, we used to force SHA-1 and 4 bits per
     * character on everyone. And of course, someone was unhappy.
     *
     * Then PHP 7.1 broke backwards-compatibility because ext/session
     * is such a mess that nobody wants to touch it with a pole stick,
     * and the one guy who does, nobody has the energy to argue with.
     *
     * So we were forced to make changes, and OF COURSE something was
     * going to break and now we have this pile of shit. -- Narf
     */
    protected function configureSidLength()
    {
        $bitsPerCharacter = (int) (ini_get('session.sid_bits_per_character') !== false
            ? ini_get('session.sid_bits_per_character')
            : 4);

        $sidLength = (int) (ini_get('session.sid_length') !== false
            ? ini_get('session.sid_length')
            : 40);

        if (($sidLength * $bitsPerCharacter) < 160) {
            $bits = ($sidLength * $bitsPerCharacter);
            // Add as many more characters as necessary to reach at least 160 bits
            $sidLength += (int) ceil((160 % $bits) / $bitsPerCharacter);
            ini_set('session.sid_length', (string) $sidLength);
        }

        // Yes, 4,5,6 are the only known possible values as of 2016-10-27
        switch ($bitsPerCharacter) {
            case 4:
                $this->sidRegexp = '[0-9a-f]';
                break;

            case 5:
                $this->sidRegexp = '[0-9a-v]';
                break;

            case 6:
                $this->sidRegexp = '[0-9a-zA-Z,-]';
                break;
        }

        $this->sidRegexp .= '{' . $sidLength . '}';
    }

    /**
     * Takes care of setting the cookie on the client side.
     *
     * @codeCoverageIgnore
     */
    protected function setCookie()
    {
        $expiration   = $this->sessionExpiration === 0 ? 0 : time() + $this->sessionExpiration;
        $this->cookie = $this->cookie->withValue(session_id())->withExpires($expiration);

        /** @var Response $response */
        $response = Services::response();
        $response->setCookie($this->cookie);
    }

    public function starttt() {
        // Iniciar la sesión PHP
        //session_start();

        // Verificar si hay una sesión activa en la base de datos
        $id = session_id();
        $sql = "SELECT * FROM sessions WHERE id = '$id'";
        $result = $this->db->query($sql);
        if ($result->rowCount() > 0) {
            // Si hay una sesión activa, actualizar el tiempo de expiración
            Cookie::set("shoropio_session_id", $id, time() + (60 * 60));
            $expiry = time() + (60 * 60); // 1 hora de vida
            $sql = "UPDATE sessions SET expiry = $expiry WHERE id = '$id'";
            $this->db->exec($sql);
        } else {
            // Si no hay una sesión activa, crear una nueva en la base de datos
            $data = '{}';
            $expiry = time() + (60 * 60); // 1 hora de vida
            Cookie::set("shoropio_session_id", $id, time() + (60 * 60));
            $sql = "INSERT into sessions (id, data, expiry) VALUES ('$id', '$data', $expiry)";
            $this->db->exec($sql);
        }
    }


    //
    public function startSessionxxxxxxxxxx() {
        /* Determine if user is logged in */
        $this->loggedIn = $this->checkIfLoggedIn();

        if ($this->loggedIn) {

        }
    }

    public function startt() {
        // Iniciar la sesión PHP
        session_start();

        

        // Verificar si hay una sesión activa en la base de datos
        $id = session_id();
        $sql = "SELECT * FROM sessions WHERE id = '$id'";
        $result = $this->db->query($sql);
        if ($result->rowCount() > 0) {
            // Si hay una sesión activa, actualizar el tiempo de expiración
            $expiry = time() + (60 * 60); // 1 hora de vida
            $sql = "UPDATE sessions SET expiry = $expiry WHERE id = '$id'";
            $this->db->exec($sql);
        } else {
            // Si no hay una sesión activa, crear una nueva en la base de datos
            $data = '{}';
            $expiry = time() + (60 * 60); // 1 hora de vida
            $sql = "INSERT into sessions (id, data, expiry) VALUES ('$id', '$data', $expiry)";
            $this->db->query($sql);
        }
    }

    

    /*
    public function checkIfLoggedIn() {
        // Verificar si hay una sesión activa
        if (isset($_SESSION['session'])) {
            // Si hay una sesión activa, verificar si el usuario está en la base de datos
            $id_usuario = $_SESSION['id_usuario'];
            $sql = "SELECT * FROM usuarios WHERE id = $id_usuario";
            $result = $this->db->query($sql);
            if ($result->rowCount() > 0) {
                // Si el usuario está en la base de datos, devolver verdadero
                return true;
            }
        }
        // Si no hay una sesión activa o el usuario no está en la base de datos, devolver falso
        return false;
    }

    */

    public function set($key, $value) {
        // Establecer un valor en la sesión PHP
        $_SESSION[$key] = $value;

        // Obtener el valor actual de la sesión PHP
        $data = json_encode($_SESSION);

        // Actualizar la sesión en la base de datos
        $id = session_id();
        $sql = "UPDATE sessions SET data = '$data' WHERE id = '$id'";
        $this->db->exec($sql);
    }

    public function get($key) {
        // Obtener un valor de la sesión PHP
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }

    public function destroy() {
        // Destruir la sesión PHP
        session_destroy();

        // Eliminar la sesión de la base de datos
        $id = session_id();
        $sql = "DELETE FROM sessions WHERE id = '$id'";
        $this->db->exec($sql);

        // Establecer la cookie de sesión con una fecha de expiración en el pasado
        setcookie(session_name(), '', time() - 3600);
    }

    public function regenerate() {
        // Regenerar el ID de sesión PHP
        session_regenerate_id(true);

        // Obtener el nuevo ID de sesión
        $id = session_id();

        // Actualizar la sesión en la base de datos
        $sql = "UPDATE sessions SET id = '$id' WHERE id = '$id'";
        $this->db->exec($sql);
    }

    public function setFlashData($key, $value) {
        // Establecer un valor temporal en la sesión PHP
        $_SESSION['flash'][$key] = $value;
    }

    public function getFlashData($key) {
        // Obtener el valor temporal de la sesión PHP
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $value;
        }
        return null;
    }

    public function setMessage($key, $value) {
        $this->set($key, $value);
    }


    public function removeMessage($key) {
        $this->remove($key);
    }

    private function remove($key) {
        session_start();
        // Eliminar el valor de la sesión PHP
        unset($_SESSION[$key]);

        // Obtener el valor actual de la sesión PHP
        $data = json_encode($_SESSION);

        // Actualizar la sesión en la base de datos
        $id = session_id();
        $sql = "UPDATE sessions SET data = '$data' WHERE id = '$id'";
        $this->db->exec($sql);
    }





}
