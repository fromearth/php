<?php
/**
 * マスタ情報
 *
 * データベースのマスタ系テーブルを用意するまでもない雑多なデータ群を配列で管理
 *
 * @filename Mst.php
 * @category MyLib
 * @package  MyLibMst
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


/**
 * マスタ情報保持クラス
 *
 * 必ず id = 1 から始める。（※0は割り当てないようにしておく。）
 * データベースのテーブル1レコードを1行に書くイメージ
 * $list = array(
 *     array(1レコード分のデータ),
 *     array(1レコード分のデータ),
 *               :
 * );
 *
 * @see Array::convMstArray()
 */
class Mst
{
    /**
     * アンケート回答用マスタ（汎用）
     * @param array 出力用
     * @return void
     */
    public static function getYesNo(&$list)
    {
        $list = array(
            array("id"=>1, "value"=>"はい"),
            array("id"=>2, "value"=>"いいえ"),
        );
    }

    /**
     * 削除フラグ選択用マスタ
     * @param array 出力用
     * @return void
     */
    public static function getDelete(&$list)
    {
        $list = array(
            array("id"=>1, "value"=>"削除"),
            array("id"=>2, "value"=>"未削除"),
        );
    }

    /**
     * ロールマスタ（役割・階級）
     * @param array 出力用
     * @return void
     */
    public static function getRole(&$list)
    {
        $list = array(
            array("id"=>1, "value"=>"guest"      , "resource"=>"1,2,3,4"),
            array("id"=>2, "value"=>"member"     , "resource"=>"5,6,7,8,9"),
            array("id"=>3, "value"=>"maintenance", "resource"=>"10"),
        );
    }

    /**
     * リソースマスタ
     * @param array 出力用
     * @return void
     */
    public static function getResource(&$list)
    {
        $list = array(
            array("id"=> 1, "value"=>"publicArea", "authority"=>"C"), // ログアウト状態
            array("id"=> 2, "value"=>"publicArea", "authority"=>"R"), // ログアウト状態
            array("id"=> 3, "value"=>"publicArea", "authority"=>"U"), // ログアウト状態
            array("id"=> 4, "value"=>"publicArea", "authority"=>"D"), // ログアウト状態
            array("id"=> 5, "value"=>"memberArea", "authority"=>"C"), // ログイン状態
            array("id"=> 6, "value"=>"memberArea", "authority"=>"R"), // ログイン状態
            array("id"=> 7, "value"=>"memberArea", "authority"=>"U"), // ログイン状態
            array("id"=> 8, "value"=>"memberArea", "authority"=>"D"), // ログイン状態
            array("id"=> 9, "value"=>"memberArea", "authority"=>"M"), // メンテナンス時
            array("id"=>10, "value"=>"memberArea", "authority"=>"A"), // メンテナンス時
        );
    }
}
