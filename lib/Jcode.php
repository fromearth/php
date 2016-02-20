<?php
/**
 * 日本語文字エンコーディング関連
 *
 * @filename Jcode.php
 * @category MyLib
 * @package  MyLibJcode
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * 日本語文字エンコーディング処理関連を扱うクラス
 *
 */
class Jcode
{
    /**
     * マルチバイト文字が含まれているか判定
     * @param string 調べたい文字列
     * @return bool 判定結果
     */
    public static function hasMultiByte($s)
    {
        return preg_match('/[\e\x80-\xfe]/', $s) === 1;
    }

    /**
     * バイナリコードが含まれているか判定
     * @param string 調べたい文字列
     * @return bool 判定結果
     */
    public static function hasBinary($s)
    {
        return preg_match('/[\x00-\x06\x7f\xff]/', $s) === 1;
    }

    /**
     * バイナリコードを除去
     * @param string 処理したい文字列
     * @return string 除去済み文字列
     */
    public static function stripBinary($s)
    {
        return preg_replace('/[\x00-\x06\x7f\xff]', '', $s);
    }

    /**
     * 文字エンコーディング判別
     * @param string 調べたい文字列
     * @return string 判別した文字エンコーディング名
     */
    public static function getCode($s)
    {
        return mb_detect_encoding($s, "ASCII,JIS,UTF-8,CP932,EUC-JP", true);
    }

    /**
     * 文字列をUTF-8に変換
     * @param string 変換したい文字列
     * @return string 変換した文字列
     */
    public static function toUtf8($s)
    {
        $enc = self::getCode($s);
        if ($enc !== "ASCII" && $enc !== "UTF-8") {
            $s = mb_convert_encoding($s, "UTF-8", $enc);
        }

        return $s;
    }

    /**
     * 文字列をJISに変換
     * @param string 変換したい文字列
     * @return string 変換した文字列
     */
    public static function toJis($s)
    {
        $enc = self::getCode($s);
        if ($enc !== "ASCII" && $enc !== "JIS") {
            $s = mb_convert_encoding($s, "JIS", $enc);
        }

        return $s;
    }

    /**
     * 文字列をSJISに変換
     * @param string 変換したい文字列
     * @return string 変換した文字列
     */
    public static function toSjis($s)
    {
        $enc = self::getCode($s);
        if ($enc !== "ASCII" && $enc !== "CP932") {
            $s = mb_convert_encoding($s, "CP932", $enc);
        }

        return $s;
    }

    /**
     * 文字列を日本語EUCに変換
     * @param string 変換したい文字列
     * @return string 変換した文字列
     */
    public static function toEuc($s)
    {
        $enc = self::getCode($s);
        if ($enc !== "ASCII" && $enc !== "EUC-JP") {
            $s = mb_convert_encoding($s, "EUC-JP", $enc);
        }

        return $s;
    }

    /**
     * 全角を半角に変換
     * @param string 変換したい文字列
     * @return string 変換した文字列
     */
    public static function z2h($s)
    {
        $enc = self::getCode($s);

        return mb_convert_kana($s, "askh", $enc);
    }

    /**
     * 半角を全角に変換
     * @param string 変換したい文字列
     * @return string 変換した文字列
     */
    public static function h2z($s)
    {
        $enc = self::getCode($s);

        return mb_convert_kana($s, "ASKHV", $enc);
    }
}
