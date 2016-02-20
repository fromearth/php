<?php
/**
 * バリデーション関連
 *
 * @filename Validate.php
 * @category MyLib
 * @package  MyLibValidate
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


/**
 * バリデーション処理クラス
 */
class Validate
{ 
    private static $errList = array();

    /**
     * データが最小値以上かどうか判定
     * @param int $data 判定したいデータ
     * @param int $min  最小値
     * @return bool 判定結果
     */
    private static function greaterEqualThan($data, $min)
    {
        return $min <= $data;
    }

    /**
     * データが最大値以下かどうか判定
     * @param int $data 判定したいデータ
     * @param int $max  最大値
     * @return bool 判定結果
     */
    private static function lessEqualThan($data, $max)
    {
        return $data <= $max;
    }

    /**
     * データが最小値から最大値までの範囲内かどうか判定
     * @param int $data 判定したいデータ
     * @param int $min  最小値
     * @param int $max  最大値
     * @return bool 判定結果
     */
    private static function between($data, $min, $max)
    {
        return self::greaterEqualThan($data, $min) && self::lessEqualThan($data, $max);
    }

    /**
     * データ文字数判定
     * @param string $data   判定したいデータ
     * @param int    $minLen 最小文字数
     * @param int    $maxLen 最大文字数
     * @return bool 判定結果
     */
    private static function betweenLen($data, $minLen, $maxLen)
    {
        // 文字数
        $len = mb_strlen($data, "UTF-8");

        return self::between($len, $minLen, $maxLen);
    }

    /**
     * データが空（空文字列 or null）かどうか判定
     * @param string $name 名前
     * @param mixed  $data 判定したいデータ
     * @return bool 判定結果
     */
    public static function isEmpty($name, $data)
    {
        if (!self::betweenLen($data, 0, 0)) {
            // 空文字列ではない場合
            self::setMes(Mes::ERR_USR_NOT_EMPTY, $name);
            return false;
        }

        return true;
    }

    /**
     * データが数値かどうか判定
     * @param string $name       名前
     * @param int    $data       判定したいデータ
     * @param int    [$min=null] 最小値
     * @param int    [$max=null] 最大値
     * @return bool 判定結果
     */
    public static function isInt($name, $data, $min=0, $max=null)
    {
        if (preg_match('/^(?:0|\-?[1-9]\d*)$/D', $data) !== 1) {
            // 数値ではない場合
            self::setMes(Mes::ERR_USR_NOT_INT, $name);
            return false;
        }

        if (is_numeric($min) && !self::greaterEqualThan($data, $min)) {
            // 最小値より小さい場合
            self::setMes(Mes::ERR_USR_NOT_INT, $name);
            return false;
        }

        if (is_numeric($max) && !self::lessEqualThan($data, $max)) {
            // 最大値より大きい場合
            self::setMes(Mes::ERR_USR_NOT_INT, $name);
            return false;
        }

        return true;
    }

    /**
     * データがID値（1以上の正数）かどうか判定
     * @param string $name       名前
     * @param int    $data       判定したいデータ
     * @param int    [$max=null] 最大値
     * @return bool 判定結果
     */
    public static function isId($name, $data, $max=null)
    {
        return self::isInt($name, $data, 1, $max);
    }

    /**
     * データが数字並びかどうか判定
     * @param string $name          名前
     * @param string $data          判定したいデータ
     * @param int    [$minLen=null] 最小桁
     * @param int    [$maxLen=null] 最大桁
     * @return bool 判定結果
     */
    public static function isNum($name, $data, $minLen=1, $maxLen=null)
    {
        if (preg_match('/^\d+$/D', $data) !== 1) {
            // 数字並びではない場合
            self::setMes(Mes::ERR_USR_NOT_NUM, $name);
            return false;
        }

        // データ長チェック
        if (!self::betweenLen($data, $minLen, $maxLen)) {
            self::setMes(Mes::ERR_USR_NOT_NUM, $name);
            return false;
        }

        return true;
    }

    /**
     * データがEメール形式かどうか判定
     * @param string $name        名前
     * @param string $data        判定したいデータ
     * @param int    [$maxLen=64] 最大文字数
     * @return bool 判定結果
     * @see filter_var($email, FILTER_VALIDATE_EMAIL)
     */
    public static function isEmail($name, $data, $maxLen=64)
    {
        // ローカル部使用可文字(RFC5321) ! # $ % & ' * + - / 0-9 = ? A-Z ^ _ ` a-z { | } ~
        $lChars = '[\x21\x23-\x27\x2a\x2b\x2d\x2f-\x39\x3d\x3f\x41-\x5a\x5e-\x7e]';

        if (preg_match('/^' . $lChars . '+(?:\.' . $lChars . '+)*'
                            . '@[\w\-]+(?:\.[\w\-]+)+$/D', $data) !== 1) {
            // email形式ではない場合
            self::setMes(Mes::ERR_USR_NOT_EMAIL, $name);
            return false;
        }

        if (!self::betweenLen($data, 5, $maxLen)) {
            // データ長範囲外の場合
            self::setMes(Mes::ERR_USR_NOT_EMAIL, $name);
            return false;
        }

        return true;
    }

    /**
     * ユーザID形式かどうか判定
     * @param string $name        名前
     * @param string $data        判定したいデータ
     * @param int    [$minLen= 8] 最小文字数
     * @param int    [$maxLen=20] 最大文字数
     * @return bool 判定結果
     */
    public static function isUserId($name, $data, $minLen=8, $maxLen=20)
    {
        if (preg_match('/^[a-z][a-z\d]*(?:-[a-z\d]+)*$/D', $data) !== 1) {
            // ユーザID形式ではない場合
            self::setMes(Mes::ERR_USR_NOT_USERID, $name);
            return false;
        }

        if (preg_match('/\d/', $data) !== 1) {
            // 数字を含まない場合
            self::setMes(Mes::ERR_USR_NOT_USERID, $name);
            return false;
        }

        // データ長チェック
        if (!self::betweenLen($data, $minLen, $maxLen)) {
            self::setMes(Mes::ERR_USR_NOT_USERID, $name);
            return false;
        }

        return true;
    }

    /**
     * バリデーションエラーが発生したか判定
     * @return bool
     */
    public static function isValid()
    {
        return empty(self::$errList);
    }

    /**
     * エラーメッセージ追加
     * @param string $s           エラーメッセージ
     * @param string [$name=null] データ名
     * @return void
     */
    private static function setMes($s, $name=null)
    {
        self::$errList[] = str_replace('%name%', $name, $s);
    }

    /**
     * エラーメッセージ取得
     * @return array
     */
    public static function getMes()
    {
        return self::$errList;
    }

    /**
     * エラーメッセージを空にする
     * @return void
     */
    public static function clearMes()
    {
        self::$errList = array();
    }
}
