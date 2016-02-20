<?php
/**
 * 算術関連
 *
 * @filename Math.php
 * @category MyLib
 * @package  MyLibMath
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * 算術関連クラス
 */
class Math
{
    // 消費税率
    const TAX_010 = 0.10;
    const TAX_008 = 0.08;
    const TAX_005 = 0.05;
    const TAX_003 = 0.03;

    /**
     * 税率の取得
     * @return float 税率
     */
    public static function getTax()
    {
        return TAX_008;
    }

    /**
     * 税額の計算
     * @param int $price 価格
     * @return int 税額
     */
    public static function calcTax($price)
    {
        $tax = self::getTax();

        return (int)($price * $tax);
    }

    /**
     * 税込み額の計算
     * @param int $price 価格
     * @return int 税込み価格
     */
    public static function calcPriceInTax($price)
    {
        return $price + self::calcTax($price);
    }
}
