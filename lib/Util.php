<?php
/**
 * ユーティリティ系処理
 *
 * @filename Util.php
 * @category MyLib
 * @package  MyLibUtil
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * ユーティリティ系処理クラス
 */
class Util
{
    /**
     * 文字列を指定長に切り詰める
     * @param  string $s             対象の文字列
     * @param  int    $length        切り詰める文字数
     * @param  string [$tail='...']  末尾付加文字指定
     * @param  string [$enc='UTF-8'] 入力文字エンコーディング指定
     * @return string 切り詰めた文字列
     */
    public static function chunk($s, $length, $tail='...', $enc='UTF-8')
    {
        return mb_strimwidth($s, 0, $length, $tail, $enc);
    }

    /**
     * 前後の空白を除去
     * @param  string $s 入力文字列
     * @return string 空白を除去した文字列
     */
    public static function trimBlank($s)
    {
        return preg_replace('/(?:^(?:\s|　)+|(?:\s|　)+$)*/u', '', $s);
    }

    /**
     * ランダム文字列生成
     * @param int    [$len=12]        文字列長
     * @param string [$addChars=null] 追加文字
     * @return string ランダム文字列
     */
    public static function randStr($len=12, $addChars=null)
    {
        $s = '';
        $charList = 'abcdefghijklmnopqrstuvwxyz'
                  . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                  . '0123456789';

        if (!empty($addChars)) {
            // 追加文字ある場合
            $charList .= $addChars;
        }

        $charLen = strlen($charList);  // 文字テンプレート長

        for ($i=0; $i<$len; $i++) {
            $s .= $charList{mt_rand(0, $charLen - 1)};
        }

        return $s;
    }

    /**
     * 自動パスワード生成
     * 読み取りづらい文字は置換
     * @param int [$len=12] 文字列長
     * @return string パスワード
     */
    public static function makePassword($len=12)
    {
        $s = strtolower(self::randStr($len));

        return str_replace(array('g', 'i', 'j', 'l', 'o', 'q', 'u', 'v', '0', '1', '8', '9'),
                           array('2', '3', '4', '5', '6', '7', 'a', 'b', 'c', 'd', 'e', 'f'), $s);
    }
}
