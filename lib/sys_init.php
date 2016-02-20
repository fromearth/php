<?php
/**
 * Webアプリケーション実行時先行処理
 *
 * @filename sys_init.php
 * @category MyLib
 * @package  MyLibSysInit
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


//------------------------------------------------
// 定数定義
//------------------------------------------------
define('TEST_MODE', true); // システム動作モード
define('MAINTE_MODE', 0);  // メンテナンス設定。(1 | 2 | 4) のようにビット和で複数管理制御
define('HOSTNAME_PROD', 'prod.hostname'); // 本番ホスト名
define('HOSTNAME_STAG', 'stag.hostname'); // ステージングホスト名
define('PS', PATH_SEPARATOR);      // 冗長につき再定義
define('DS', DIRECTORY_SEPARATOR); // 冗長につき再定義


//------------------------------------------------
// エラー・警告類表示制御
//------------------------------------------------
if (isTestMode()) {
    // テストモードの場合 / エラー・警告類すべて画面表示
    ini_set('error_reporting', -1);
    ini_set('display_errors', 1);
    ini_set('log_errors', 0);
} else {
    // テストモードでない場合 / ログ出力のみ
    ini_set('error_reporting', 0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}


//------------------------------------------------
// 通したいパスを追加
//------------------------------------------------
$path = array(
    '.',
    '/path/to/lib',
);
set_include_path(implode(PS, array_map('realpath', $path)).PS.get_include_path());


//------------------------------------------------
// インクルードファイル
//------------------------------------------------
require_once 'autoloader.php';
require_once 'Date.php';
require_once 'shortcut_funcs.php';
require_once 'error.php';
//require_once 'SessHandler.php';


//------------------------------------------------
// ホスト名を取得
//------------------------------------------------
function getHostname() {
    static $hostname;

    if (empty($hostname)) {
        $hostname = php_uname('n');
    }

    return $hostname;
}


//------------------------------------------------
// 本番環境かどうか判定
//------------------------------------------------
function isProduction() {
    return getHostname() === HOSTNAME_PROD;
}


//------------------------------------------------
// ステージング環境かどうか判定
//------------------------------------------------
function isStaging() {
    return getHostname() === HOSTNAME_STAG;
}


//------------------------------------------------
// 開発環境かどうか判定
//------------------------------------------------
function isDevelopment() {
    return !isStaging() && !isProduction();
}


//------------------------------------------------
// 環境名を取得
//------------------------------------------------
function getEnvironment() {
    if (isProduction()) {
        return 'product';
    } else if (isStaging()) {
        return 'staging';
    } else {
        return 'devel';
    }
}


//------------------------------------------------
// テストモード判定
// 本番環境以外でテストモード可
//------------------------------------------------
function isTestMode() {
    return !isProduction() && !(defined('TEST_MODE') && !TEST_MODE);
}
