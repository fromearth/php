<?php
/**
 * User-Agent情報解析関連
 *
 * @filename UA.php
 * @category MyLib
 * @package  MyLibUA
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * User-Agent情報解析関連クラス
 */
class UA
{
    private static $ua;
    private static $device;
    private static $type;
    private static $carrier;

    /**
     * デバイス取得
     * @param string [$ua=null] User-Agent
     * @return string デバイス
     */
    public static function getDevice($ua=null)
    {
        self::setUserAgent($ua);

        self::scanDevice();

        return self::$device;
    }

    /**
     * デバイスタイプ取得
     * @param string [$ua=null] User-Agent
     * @return string デバイスタイプ
     */
    public static function getType($ua=null)
    {
        self::getDevice($ua);

        return self::$type;
    }

    /**
     * キャリア取得
     * @param string [$ua=null] User-Agent
     * @return string キャリア
     */
    public static function getCarrier($ua=null)
    {
        self::setUserAgent($ua);

        self::scanCarrier();

        return self::$carrier;
    }

    /**
     * User-Agent取得
     * @return string User-Agent
     */
    private static function getUserAgent()
    {
        if (empty(self::$ua)) {
            self::$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }

        return self::$ua;
    }

    /**
     * User-Agentをセット
     * @param string $ua User-Agent
     * @return void
     */
    private static function setUserAgent($ua)
    {
        if (!is_null($ua)) {
            self::$ua = $ua;
        }
    }

    /**
     * デバイスおよびデバイスタイプ解析
     * @return void
     */
    private static function scanDevice()
    {
        $UA = self::getUserAgent();

        if (strpos($UA, 'iPhone') !== false) {
            // iPhone
            self::$device = 'iphone';
            self::$type = 'sp';
        } elseif (strpos($UA, 'iPad') !== false) {
            // iPad
            self::$device = 'ipad';
            self::$type = 'tb';
        } elseif (strpos($UA, 'Android') !== false) {
            // Android
            self::$device = 'android';
            if (strpos($UA, 'Mobile') !== false) {
                // Android(SmartPhone)
                self::$type = 'sp';
            } else {
                // Android(Tablet)
                self::$type = 'tb';
            }
        } elseif (strpos($UA, 'Windows Phone') !== false) {
            // WindowsPhone
            self::$device = 'windows_phone';
            self::$type = 'sp';
        } elseif (strpos($UA, 'BlackBerry') !== false) {
            // BlackBerry
            self::$device = 'black_berry';
            self::$type = 'sp';
        } elseif (!empty($UA)) {
            // PC
            self::$device = 'pc';
            self::$type = 'pc';
        } else {
            // 不明
            self::$device = 'unknown';
            self::$type = 'pc';
        }
    }

    /**
     * キャリア解析
     * @return void
     */
    private static function scanCarrier()
    {
        $UA = self::getUserAgent();

        if (strpos($UA, 'DoCoMo') !== false) {
            // DoCoMo
            self::$carrier = 'docomo';
        } elseif (strpos($UA, 'UP.Browser') !== false) {
            // au
            self::$carrier = 'au';
        } elseif (preg_match('/SoftBank|Vodafone|J-PHONE|SMOT/', $UA) === 1) {
            // SoftBank
            self::$carrier = 'softbank';
        } elseif (strpos($UA, 'WILLCOM') !== false) {
            // WILLCOM
            self::$carrier = 'willcom';
        } elseif (strpos($UA, 'emobile') !== false) {
            // eモバイル
            self::$carrier = 'emobile';
        } else {
            // 不明
            self::$carrier = 'unknown';
        }
    }
}
