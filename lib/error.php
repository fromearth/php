<?php
/**
 * 実行時エラー処理
 *
 * @filename error.php
 * @category MyLib
 * @package  MyLibError
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


//-----------------------------------------------
// PHPエラー発生時の処理定義
//-----------------------------------------------
function myErrorHandler($errNo, $errStr, $file, $line) {

    switch ($errNo) {
    case E_USER_ERROR:
        echo 'error';
        break;
    case E_USER_WARNING:
        echo 'warning';
        break;
    case E_USER_NOTICE:
        echo 'notice';
        break;
    default:
        echo 'unknown';
        break;
    }

    return true;
}

//-----------------------------------------------
// 例外スロー時の処理定義
//-----------------------------------------------
function myExceptionHandler($e) {

    $message = $e->getMessage();
    if ($e->getCode() != 0) {
        // 画面表示不可メッセージの場合
        $message = Mes::getMessage($e->getCode()); // 汎用メッセージ取得

        // ログ採り
    }

    switch (get_class($e)) {
    case 'SysException':
        break;
    case 'UsrException':
        break;
    default:
        break;
    }

    echo $message;
}


// 実装
set_error_handler("myErrorHandler");
set_exception_handler('myExceptionHandler');

