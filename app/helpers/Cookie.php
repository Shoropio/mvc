<?php

namespace App\Helpers;

class Cookie {

    /**
     * Cookies will be sent in all contexts, i.e in responses to both
     * first-party and cross-origin requests. If `SameSite=None` is set,
     * the cookie `Secure` attribute must also be set (or the cookie will be blocked).
     */
    public const SAMESITE_NONE = 'none';

    /**
     * Cookies are not sent on normal cross-site subrequests (for example to
     * load images or frames into a third party site), but are sent when a
     * user is navigating to the origin site (i.e. when following a link).
     */
    public const SAMESITE_LAX = 'lax';

    /**
     * Cookies will only be sent in a first-party context and not be sent
     * along with requests initiated by third party websites.
     */
    public const SAMESITE_STRICT = 'strict';

    /**
     * RFC 6265 allowed values for the "SameSite" attribute.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite
     */
    public const ALLOWED_SAMESITE_VALUES = [
        self::SAMESITE_NONE,
        self::SAMESITE_LAX,
        self::SAMESITE_STRICT,
    ];

    /**
     * Expires date format.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Date
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.1.2
     */
    public const EXPIRES_FORMAT = 'D, d-M-Y H:i:s T';

    /**
     * @var string
     */
    private $prefix = '';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $expires;

    /**
     * @var string
     */
    private $path = '/';

    /**
     * @var string
     */
    private $domain = '';

    /**
     * @var bool
     */
    private $secure = false;

    /**
     * @var bool
     */
    private $httponly = true;

    /**
     * @var string
     */
    private $samesite = self::SAMESITE_LAX;

    /**
     * @var bool
     */
    private $raw = false;

    /**
     * Default attributes for a Cookie object. The keys here are the
     * lowercase attribute names. Do not camelCase!
     *
     * @var array<string, mixed>
     */
    private static array $defaults = [
        'prefix'   => '',
        'expires'  => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => false,
        'httponly' => true,
        'samesite' => self::SAMESITE_LAX,
        'raw'      => false,
    ];

    /**
     * A cookie name can be any US-ASCII characters, except control characters,
     * spaces, tabs, or separator characters.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#attributes
     * @see https://tools.ietf.org/html/rfc2616#section-2.2
     */
    private static string $reservedCharsList = "=,; \t\r\n\v\f()<>@:\\\"/[]?{}";

    public function __construct(string $name, string $value = '', array $options = [])
    {
        $options += self::$defaults;

        $options['expires'] = static::convertExpiresTimestamp($options['expires']);

        // If both `Expires` and `Max-Age` are set, `Max-Age` has precedence.
        if (isset($options['max-age']) && is_numeric($options['max-age'])) {
            $options['expires'] = time() + (int) $options['max-age'];
            unset($options['max-age']);
        }

        // to preserve backward compatibility with array-based cookies in previous CI versions
        $prefix = ($options['prefix'] === '') ? self::$defaults['prefix'] : $options['prefix'];
        $path   = $options['path'] ?: self::$defaults['path'];
        $domain = $options['domain'] ?: self::$defaults['domain'];

        // empty string SameSite should use the default for browsers
        $samesite = $options['samesite'] ?: self::$defaults['samesite'];

        $raw      = $options['raw'];
        $secure   = $options['secure'];
        $httponly = $options['httponly'];

        $this->validateName($name, $raw);
        $this->validatePrefix($prefix, $secure, $path, $domain);
        $this->validateSameSite($samesite, $secure);

        $this->prefix   = $prefix;
        $this->name     = $name;
        $this->value    = $value;
        $this->expires  = static::convertExpiresTimestamp($options['expires']);
        $this->path     = $path;
        $this->domain   = $domain;
        $this->secure   = $secure;
        $this->httponly = $httponly;
        $this->samesite = ucfirst(strtolower($samesite));
        $this->raw      = $raw;
    }

    /**
     * {@inheritDoc}
     */
    public function getSameSite(): string
    {
        return $this->samesite;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * {@inheritDoc}
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * Establece una cookie en el navegador del usuario.
     *
     * @param string $name Nombre de la cookie.
     * @param mixed $value Valor de la cookie.
     * @param int $expiry Tiempo de expiración en segundos.
     * @param string $path Directorio donde la cookie es válida.
     * @param string $domain Dominio donde la cookie es válida.
     * @param bool $secure Indica si la cookie sólo debe enviarse a través de una conexión segura.
     * @param bool $httpOnly Indica si la cookie sólo debe accederse a través del protocolo HTTP.
     * @return bool True si la cookie se estableció correctamente, false en caso contrario.
     */
    public static function set($name, $value, $expiry = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false) {
        return setcookie($name, serialize($value), $expiry, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Obtiene el valor de una cookie.
     *
     * @param string $name Nombre de la cookie.
     * @return mixed Valor de la cookie o null si la cookie no existe.
     */
    public static function gettttt($name) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    /**
     * Obtiene el valor de una cookie.
     *
     * @param string $name Nombre de la cookie.
     * @return mixed El valor de la cookie, o null si la cookie no existe.
     */
    public static function get($name) {
        return self::exists($name) ? $_COOKIE[$name] : null;
    }

    /**
     * Elimina una cookie estableciendo su tiempo de expiración en el pasado.
     *
     * @param string $name Nombre de la cookie.
     * @param string $path Directorio donde la cookie es válida.
     * @param string $domain Dominio donde la cookie es válida.
     * @param bool $secure Indica si la cookie sólo debe enviarse a través de una conexión segura.
     * @param bool $httpOnly Indica si la cookie sólo debe accederse a través del protocolo HTTP.
     * @return bool True si la cookie se eliminó correctamente, false en caso contrario.
     */
    public static function delete($name, $path = '/', $domain = '', $secure = false, $httpOnly = false) {
        return setcookie($name, '', time() - 3600, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Verifica si una cookie existe y su valor es válido.
     *
     * @param string $name Nombre de la cookie.
     * @return bool True si la cookie existe y su valor es válido, false en caso contrario.
     */
    public static function exists($name) {
        return isset($_COOKIE[$name]) && !empty($_COOKIE[$name]);
    }

    /**
     * Converts expires time to Unix format.
     *
     * @param DateTimeInterface|int|string $expires
     */
    protected static function convertExpiresTimestampfffff($expires = 0): int
    {
        if ($expires instanceof DateTimeInterface) {
            $expires = $expires->format('U');
        }

        if (! is_string($expires) && ! is_int($expires)) {
            throw CookieException::forInvalidExpiresTime(gettype($expires));
        }

        if (! is_numeric($expires)) {
            $expires = strtotime($expires);

            if ($expires === false) {
                throw CookieException::forInvalidExpiresValue();
            }
        }

        return $expires > 0 ? (int) $expires : 0;
    }

    protected static function convertExpiresTimestamp($expires) {
        if ($expires instanceof \DateTime) {
            return $expires->format('U');
        } elseif (is_numeric($expires)) {
            return $expires;
        } else {
            $expires = strtotime($expires);
            if ($expires === false) {
                throw new \InvalidArgumentException('Invalid expires time');
            }
            return $expires;
        }
    }

    /**
     * Valida el nombre de una cookie.
     *
     * @param string $name Nombre de la cookie.
     * @return bool True si el nombre es válido, false en caso contrario.
     */
    protected static function validateName($name) {
        // El nombre de la cookie debe tener entre 1 y 32 caracteres y no puede contener caracteres especiales
        //return preg_match('/^[a-zA-Z0-9_-]{1,}$/', $name) === 1;
        return preg_match('/^[a-zA-Z0-9_]{1,32}$/', $name) === 1;
    }

    /**
     * Valida que el prefijo de una cookie sea válido.
     *
     * @param string $prefix Prefijo de la cookie a validar.
     * @return bool True si el prefijo es válido, false en caso contrario.
     */
    protected static function validatePrefix($prefix) {
        return preg_match('/^[a-zA-Z0-9_-]{0,}$/', $prefix) === 1;
    }

    /**
     * Valida el valor de la opción SameSite de una cookie.
     *
     * @param mixed $sameSite Valor de la opción SameSite de la cookie.
     * @return bool True si el valor es válido, false en caso contrario.
     */
    protected static function validateSameSite($sameSite)
    {
        return in_array($sameSite, ['Lax', 'Strict', 'None'], true);
    }

}



/*
class Cookie {
    // nombre de la cookie
    private $name;

    // duración de la cookie en segundos
    private $duration;

    public function __construct() {
        //$this->name = $name;
        //$this->duration = $duration;
    }

    public function setCookie($name, $value, $duration) {
        //setcookie($name, $value, time() + $duration);
        setcookie($name, serialize($value), time() + $duration, '/', '', false, true);
    }

    // crear la cookie de inicio de sesión
    public function create($user) {
        setcookie($this->name, serialize($user), time() + $this->duration, '/');

        /* setcookie($this->name, serialize($user), time() + $this->duration, '/', '', false, true);
     
    }

    // obtener el usuario almacenado en la cookie
    public function get() {
        if (isset($_COOKIE[$this->name])) {
          return unserialize($_COOKIE[$this->name]);
        } else {
          return false;
        }
    }

    // eliminar la cookie de inicio de sesión
    public function delete() {
        setcookie($this->name, '', time() - 3600, '/');
    }
}*/