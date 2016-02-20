<?php
/**
 * 配列処理関連
 *
 * @filename Array.php
 * @category MyLib
 * @package  MyLibArray
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * 配列処理関連クラス
 */
class Array
{
    /**
     * マスタ配列の変換
     * @see Mst.php
     * @param array  $inMst        入力マスタ配列
     * @param array  $outMst       出力マスタ配列
     * @param string [$kKey=id]    キーとしてセットする入力マスタ配列キー
     * @param string [$vKey=value] 値としてセットする入力マスタ配列キー
     * @return void
     */
    public static function convMstArray(&$inMst, &$outMst, $kKey='id', $vKey='value')
    {
        self::alwaysArray($inMst);

        foreach ($inMst as $key => $val) {
            $outMst[$val[$kKey]] = $val[$vKey];
        }
    }

    /**
     * 配列型以外なら空配列で初期化する
     * @param mixed $var 入力変数
     * @return void
     */
    public static function alwaysArray(&$var)
    {
        if (empty($var) || !is_array($var)) {
            $var = array();
        }
    }
}
