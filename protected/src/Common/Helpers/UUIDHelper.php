<?php
/**
 * @author    Maxim Kirichenko
 * @copyright Copyright (c) 2009-2017 Maxim Kirichenko (kirichenko.maxim@gmail.com)
 * @license   GNU General Public License http://www.gnu.org/licenses
 */


namespace Application\Common\Helpers;


/**
 * UUID Helper Class
 *
 * This implements the abilities to create UUID's for Avant.
 * Code has been borrowed from the following comments on php.net
 * and has been optimized for Avant use.
 * http://www.php.net/manual/en/function.uniqid.php#94959
 *
 * @author  Dan Storm, Maxim Kirichenko
 * @link    http://catalystcode.net/
 * @license GNU LPGL
 * @version 2.1
 */

class UUIDHelper
{
    public static function format($trim = FALSE) {

        $format = ($trim == FALSE) ? '%04x%04x-%04x-%04x-%04x-%04x%04x%04x' : '%04x%04x%04x%04x%04x%04x%04x%04x';

        return sprintf($format,

          // 32 bits for "time_low"
          mt_rand(0, 0xffff), mt_rand(0, 0xffff),

          // 16 bits for "time_mid"
          mt_rand(0, 0xffff),

          // 16 bits for "time_hi_and_version",
          // four most significant bits holds version number 4
          mt_rand(0, 0x0fff) | 0x4000,

          // 16 bits, 8 bits for "clk_seq_hi_res",
          // 8 bits for "clk_seq_low",
          // two most significant bits holds zero and one for variant DCE1.1
          mt_rand(0, 0x3fff) | 0x8000,

          // 48 bits for "node"
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public static function isValid($uuid) {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }

    public static function v3($name, $namespace = NULL) {
        if (is_null($namespace))
            $namespace = self::format();

        if (empty($name))
            return FALSE;

        if (!self::isValid($namespace))
            return FALSE;

        // Get hexadecimal components of namespace
        $nhex = str_replace(['-', '{', '}'], '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for ($i = 0; $i < strlen($nhex); $i += 2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }

        // Calculate hash value
        $hash = md5($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',

          // 32 bits for "time_low"
          substr($hash, 0, 8),

          // 16 bits for "time_mid"
          substr($hash, 8, 4),

          // 16 bits for "time_hi_and_version",
          // four most significant bits holds version number 3
          (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

          // 16 bits, 8 bits for "clk_seq_hi_res",
          // 8 bits for "clk_seq_low",
          // two most significant bits holds zero and one for variant DCE1.1
          (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

          // 48 bits for "node"
          substr($hash, 20, 12)
        );
    }

    public static function v5($name, $namespace = NULL) {
        if (is_null($namespace))
            $namespace = self::format();

        if (empty($name))
            return FALSE;

        if (!self::isValid($namespace))
            return FALSE;

        // Get hexadecimal components of namespace
        $nhex = str_replace(['-', '{', '}'], '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for ($i = 0; $i < strlen($nhex); $i += 2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }

        // Calculate hash value
        $hash = sha1($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',

          // 32 bits for "time_low"
          substr($hash, 0, 8),

          // 16 bits for "time_mid"
          substr($hash, 8, 4),

          // 16 bits for "time_hi_and_version",
          // four most significant bits holds version number 5
          (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

          // 16 bits, 8 bits for "clk_seq_hi_res",
          // 8 bits for "clk_seq_low",
          // two most significant bits holds zero and one for variant DCE1.1
          (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

          // 48 bits for "node"
          substr($hash, 20, 12)
        );
    }

    public static function rand($length = 16) {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($length / 2));
        }
        elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        }
        else {
            throw new \Exception("Not found cryptographically secure random function available");
        }

        return substr(bin2hex($bytes), 0, $length);
    }
}

/* End of file UUID.php */
