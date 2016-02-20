<?php
/**
 * ネットワーク通信関連
 *
 * @filename Net.php
 * @category MyLib
 * @package  MyLibNet
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * ネットワーク通信関連クラス
 */
class Net
{
    /**
     * Ajax通信かどうか判定
     * @return bool 判定値
     */
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(
                     $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * JSON文字列データに変換してレスポンス
     * @param mixed $data              返却データ
     * @param array $errList           エラー配列
     * @param bool  [$headerOff=false] ヘッダ抑制フラグ
     * @return void
     */
    public static function responseJson($data, $errList, $headerOff = false)
    {
        if (!$headerOff) {
            // ヘッダ抑制フラグがfalseの場合
            header('Content-Type: application/json; charset=utf-8');
        }

        // 返却データが配列ではない場合、dataキーの配列に
        if (!is_array($data)) $data = array("data" => $data);

        $list = array(
            "success" => (int)!empty($errList),
            "error_reason" => $errList,
        );

        // 2配列を結合しJSON文字列変換して出力
        echo json_encode($list + $data);
    }

    /**
     * $_SERVER変数の値を取得
     * @param string $key            キー指定
     * @param mixed  [$default=null] デフォルト値
     * @return mixed
     */
    public static function getServer($key, $default=null)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    /**
     * $_REQUEST変数の値を取得
     * @param string $key            キー指定
     * @param mixed  [$default=null] デフォルト値
     * @return mixed
     */
    public static function getRequest($key, $default=null)
    {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }

    /**
     * $_POST変数の値を取得
     * @param string $key            キー指定
     * @param mixed  [$default=null] デフォルト値
     * @return mixed
     */
    public static function getPost($key, $default=null)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * $_GET変数の値を取得
     * @param string $key            キー指定
     * @param mixed  [$default=null] デフォルト値
     * @return mixed
     */
    public static function getGet($key, $default=null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * $_COOKIE変数の値を取得
     * @param string $key            キー指定
     * @param mixed  [$default=null] デフォルト値
     * @return mixed
     */
    public static function getCookie($key, $default=null)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }
}
