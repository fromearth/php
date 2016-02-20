<?php
/**
 * オートローダー
 *
 * @filename autoloader.php
 * @category MyLib
 * @package  MyLibAutoloader
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


//-------------------------------------------------
// 未ロードのクラスがあった場合に行う処理
// @param string $class クラス名が渡されてくる
// @return void
//-------------------------------------------------
function my_autoloader($class) {
    include str_replace('_', '/', $class).'.php';
}

// 実装
spl_autoload_register('my_autoloader');
