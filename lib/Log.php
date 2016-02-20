<?php
/**
 * ログ関連
 *
 * @filename Log.php
 * @category MyLib
 * @package  MyLibLog
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * ログ関連クラス
 */
class Log
{
    // ログ書き出しファイル
    const FILE_DEBUG_LOG = '/tmp/.debug.log';
    // 開始見出し
    const IDENT_START = 'DEBUG_001';

    /**
     * デバッグ（ファイル出力）
     * @param mixed  $data                        デバッグ変数
     * @param bool   [$append=true]               追記モードOn/Off
     * @param string [$path=self::FILE_DEBUG_LOG] 出力先ファイルパス指定
     * @param string [$ident=self::IDENT_START]   開始見出し
     * @return void
     */
    public static function debug($data, $append=true, $path=self::FILE_DEBUG_LOG, $ident=self::IDENT_START)
    {
        global $argv;
        static $sIdent;

        if (empty($sIdent)) {
            // 見出しが未定義の場合(=初回呼出し）
            $sIdent = $ident;
            $firstCall = true;
        } else {
            $firstCall = false;
        }

        // ファイルを開く
        $fp = fopen($path, $append ? "a" : "w");
        if (!$fp) return;

        // ヘッダ
        if ($firstCall) {
            // 初回呼出しの場合
            $date = date('Y-m-d H:i:s');
            $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $argv[0];
            fwrite($fp, "\n[{$date}] ------------------------------------- {$uri}\n");
            fwrite($fp, self::makeGlobalsData());  // グローバル変数データ
        }

        // データ
        if (!is_string($data)) $data = var_export($data, true);
        fwrite($fp, $sIdent . ") " . rtrim($data) . "\n");

        // 直近のエラー・警告
        $err = error_get_last();
        if (!empty($err)) {
            $err = var_export($err, true);
            fwrite($fp, rtrim($err) . "\n");
        }

        // ファイルを閉じる
        fclose($fp);

        $sIdent++;
    }

    /**
     * デバッグ（HTMLコメント出力）
     * @param mixed  $data                      デバッグ変数
     * @param string [$ident=self::IDENT_START] 開始見出し
     * @return void
     */
    public static function print_r($data, $ident=self::IDENT_START)
    {
        static $sIdent;

        if (empty($sIdent)) {
            // 見出しが未定義の場合(=初回呼出し）
            $sIdent = $ident;
            $firstCall = true;
        } else {
            $firstCall = false;
        }

        // コメント開始
        echo "<!-- \n";

        // データ
        if (!is_string($data)) $data = var_export($data, true);
        if ($firstCall) {
            // 初回呼出しの場合
            echo self::makeGlobalsData();  // グローバル変数データ
        }
        echo $sIdent . ") " . rtrim($data) . "\n";

        // 直近のエラー・警告
        $err = error_get_last();
        if (!empty($err)) {
            $err = var_export($err, true);
            echo rtrim($err) . "\n";
        }

        // コメント終了
        echo " -->\n";

        $sIdent++;
    }

    /**
     * スーパーグローバル変数データ成形
     * @return string 成形した文字列
     */
    private static function makeGlobalsData()
    {
        $res = '';

        if (!empty($_GET)) {
            $res .= '$_GET = ' . rtrim(var_export($_GET, true)) . "\n";
        }
        if (!empty($_POST)) {
            $res .= '$_POST = ' . rtrim(var_export($_POST, true)) . "\n";
        }
        if (!empty($_COOKIE)) {
            $res .= '$_COOKIE = ' . rtrim(var_export($_COOKIE, true)) . "\n";
        }
        if (!empty($_SESSION)) {
            $res .= '$_SESSION = ' . rtrim(var_export($_SESSION, true)) . "\n";
        }
        if (!empty($_FILES)) {
            $res .= '$_FILES = ' . rtrim(var_export($_FILES, true)) . "\n";
        }

        return $res;
    }

    /**
     * システムログへメッセージを書き出す
     * @param  string $message  メッセージ
     * @param  string $ident    見出し
     * @param  int    $facility メッセージ型
     * @param  int    $level    ログレベル
     * @return void
     */
    public static function syslog($message, $ident='Script', $facility=LOG_USER, $level=LOG_INFO)
    {
        openlog($ident, LOG_NDELAY, $facility);
        syslog($level, $message);
        closelog();
    }

    /**
     * PHPエラーログ（設定によってはシステムログ）
     * @param string $message     メッセージ
     * @param string [$path=null] 出力先ファイル指定
     * @return void
     */
    public static function error($message, $path=null)
    {
        if (is_null($path)) {
            // パス指定なければ
            error_log(rtrim($message) . "\n", 0);        // PHPエラーログ
        } else {
            // パス指定あれば
            error_log(rtrim($message) . "\n", 3, $path); // カスタムログ
        }
    }
}
