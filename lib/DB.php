<?php
/**
 * データベース接続・操作関連
 *
 * @filename DB.php
 * @category MyLib
 * @package  MyLibDB
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * データベース接続・操作関連クラス
 *
 * @example
 * <code>
 * // MasterDB Connect
 * $row = DB::connMaster()->query('SELECT NOW() AS now')->fetch();
 * var_dump($row['now']);
 * </code>
 *
 * <code>
 * // SlaveDB Connect
 * $conn = DB::connSlave(DB::DATABASE_INI, 'devel');
 * $stmt = $conn->query("SELECT NOW()");
 * var_dump($stmt->fetch());
 * </code>
 *
 * <code>
 * // General Connect And PlaceHolder
 * $conn = DB::conn('mysql://root@localhost/test?charset=utf8');
 * $stmt = $conn->prepare("SELECT 'foo' = :param1");
 * $param1 = 'foo';
 * $stmt->bindParam(":param1", $param1);
 * $stmt->execute();
 * var_dump($stmt->fetch());
 * </code>
 */
class DB
{
    // 接続情報設定ini
    const DATABASE_INI = '../data/database.ini';

    // 接続を保持
    private static $conn;

    /**
     * コンストラクタ
     */
    private function __construct() {
    }

    /**
     * データベースマスター接続
     * @param string [$file=null] 接続URI情報iniファイル
     * @param string [$env=null]  動作環境
     * @return resource 接続リソース
     */
    public static function connMaster($file=null, $env=null)
    {
        if (empty($file)) {
            $file = self::DATABASE_INI;
        }

        $uri = self::getMasterUri($file, $env);

        return self::conn($uri);
    }

    /**
     * データベーススレーブ接続
     * @param string [$file=null] 接続URI情報iniファイル
     * @param string [$env=null]  動作環境
     * @return resource 接続リソース
     */
    public static function connSlave($file=null, $env=null)
    {
        if (empty($file)) {
            $file = self::DATABASE_INI;
        }

        $uri = self::getSlaveUri($file, $env);

        return self::conn($uri);
    }

    /**
     * データベース接続
     * @param string $uri 接続URI情報
     * @return resource 接続リソース
     */
    public static function conn($uri)
    {
        if (empty(self::$conn[$uri])) {
            // 未接続の場合
            $tmp = self::parseUri($uri); // 接続URI情報分解
            if (count($tmp) !== 4) {
                // 分解できなかった場合
                return null;
            }

            try {
                self::$conn[$uri] = new PDO($tmp[0], $tmp[1], $tmp[2], $tmp[3]); // 接続
            } catch (Exception $e) {
                // 接続時に例外スロー発生
                return null;
            }
        }

        return self::$conn[$uri];
    }

    /**
     * 接続URI情報を分解して返却
     * @param string $uri 接続URI情報
     * @return array 接続情報配列
     */
    private static function parseUri($uri)
    {
        // mysql://username[:password]@hostname[:port]/dbname[?charset=utf8]
        if (preg_match('!://([^:@\s]+)(?::([^@\s]*))?@([^/:\s]+)(?::(\d*))?'
                . '/([^/\?\s]+)(?:\?charset=([\w\-]+))?!i', $uri, $matched) !== 1) {
            // 接続URIの分解
            return array();
        }

        $user = $matched[1];
        $pass = $matched[2];

        $dsn = "mysql:host={$matched[3]};dbname={$matched[5]}";

        if ($matched[4] != "") {
            // ポート指定あれば
            $dsn .= ";port={$matched[4]}";
        }

        $attr = array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // カラム名をキーとする連想配列のみで取得指定
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // エラー時に例外スローさせる
        );
        if (!empty($matched[6])) {
            // 文字エンコーディング指定あれば
            $attr[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$matched[6]}";
        }

        return array($dsn, $user, $pass, $attr);
    }

    /**
     * 動作環境のデータベース接続URI情報リスト取得
     * @param string $file        接続URI情報iniファイル指定
     * @param string [$name=null] iniパラメータ名
     * @param string [$env=null]  環境
     * @return array
     */
    private static function getUriList($file, $name=null, $env=null)
    {
        if (!is_readable($file)) {
            // ファイルが読み込み不可なら
            return array();
        }
        $list = parse_ini_file($file, true);

        if (is_null($env)) {
            // 環境指定ない場合
            if (function_exists('getEnvironment')) {
                $env = getEnvironment();
            } else {
                $env = 'devel';
            }
        }

        if (isset($list[$env])) {
            // 環境のURI取得できた場合
            if (is_null($name)) {
                // パラメータ指定ない場合
                return $list[$env];
            } else if (isset($list[$env][$name])) {
                // パラメータ指定があり、URI取得できている場合
                return $list[$env][$name];
            }
        }

        return array();
    }

    /**
     * マスタDB接続URI情報取得
     * @param string $file       接続URI情報iniファイル指定
     * @param string [$env=null] 環境
     * @return string 接続URI情報
     */
    public static function getMasterUri($file, $env=null)
    {
        $masterDB = self::getUriList($file, 'master', $env);
        if (empty($masterDB)) {
            // 取得できなかった場合
            return;
        }

        // 台数
        $count = count($masterDB);

        if ($count === 1) {
            // サーバが1台の場合
            return $masterDB[0];
        }

        // 複数台の中からランダムに返す
        return $masterDB[mt_rand(0, $count - 1)];
    }

    /**
     * スレーブDB接続URI情報取得
     * @param string $file       接続URI情報iniファイル指定
     * @param string [$env=null] 環境
     * @return string 接続URI情報
     */
    public static function getSlaveUri($file, $env=null)
    {
        $slaveDB = self::getUriList($file, 'slave', $env);
        if (empty($slaveDB)) {
            // 取得できなかった場合
            return;
        }

        // 台数
        $count = count($slaveDB);

        if ($count === 1) {
            // サーバが1台の場合
            return $slaveDB[0];
        }

        // 複数台の中からランダムに返す
        return $slaveDB[mt_rand(0, $count - 1)];
    }

    /**
     * LIKE検索ワードのエスケープ処理
     * @param string $s 検索キーワード
     * @return string LIKE用エスケープ済み文字列
     */
    public static function likeEscape($s)
    {
        return str_replace(array('\\', '%', '_'),
                           array('\\\\', '\\%', '\\_'), $s);
    }
}
