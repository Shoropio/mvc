<?php

namespace App\Helpers;

class Password {
    // Longitud mínima recomendada para las contraseñas
    const MIN_LENGTH = 8;

    /**
     * Encripta una contraseña utilizando una función hash segura.
     *
     * @param string $password La contraseña a encriptar.
     * @return string La contraseña encriptada.
     */
    public static function hash($password) {
        // Utilizamos la función password_hash() de PHP para encriptar la contraseña
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verifica si una contraseña es válida comparándola con su hash.
     *
     * @param string $password La contraseña a verificar.
     * @param string $hash El hash de la contraseña.
     * @return bool True si la contraseña es válida, false en caso contrario.
     */
    public static function verify($password, $hash) {
        // Utilizamos la función password_verify() de PHP para verificar la contraseña
        return password_verify($password, $hash);
    }

    /**
     * Verifica si una contraseña cumple con los requisitos de seguridad.
     *
     * @param string $password La contraseña a verificar.
     * @return bool True si la contraseña es segura, false en caso contrario.
     */

    public static function isSecure($password) {
        // Verificamos que la contraseña tenga una longitud mínima recomendada
        if (strlen($password) < self::MIN_LENGTH) {
            return false;
        }

        // Verificamos que la contraseña tenga al menos un caracter en mayúsculas, minúsculas y números
        $hasUppercase = preg_match('/[A-Z]/', $password);
        $hasLowercase = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        if (!$hasUppercase || !$hasLowercase || !$hasNumber) {
            return false;
        }

        return true;
    }
}
