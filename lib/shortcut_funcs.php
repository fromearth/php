<?php
/**
 * ショートカット関数群
 *
 * 既存クラスの中で多用するメソッドを、呼びやすいよう関数化しグローバルに使いやすくしておく
 *
 * @filename shortcut_funcs.php
 * @category MyLib
 * @package  MyLibShortcutFuncs
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


//----------------------------------
// タグを無害化 / サニタイズ
// @see Html.php
//----------------------------------
function h($s, $enc = 'UTF-8') {
    return Html::h($s, $enc);
}


//----------------------------------
// スーパーグローバル変数の値取得
// @see Net.php
//----------------------------------
function getServer($key, $default = null) {
    return Net::getServer($key, $default);
}
function getRequest($key, $default = null) {
    return Net::getRequest($key, $default);
}
function getPost($key, $default = null) {
    return Net::getPost($key, $default);
}
function getGet($key, $default = null) {
    return Net::getGet($key, $default);
}
function getCookie($key, $default = null) {
    return Net::getCookie($key, $default);
}


//----------------------------------
// バリデーション
// @see Validate.php
//----------------------------------
function isId($n) {
    // 1から始まる数値系IDチェック用
    return Validate::isId(null, $n);
}


//----------------------------------
// マスタ配列の変換
// @see Array.php
//----------------------------------
function convMstArray(&$inMst, &$outMst, $kKey='id', $vKey='value') {
    Array::convMstArray($inMst, $outMst, $kKey, $vKey);
}


//----------------------------------
// 配列型以外なら空配列で初期化
// @see Array.php
//----------------------------------
function alwaysArray(&$var) {
    Array::alwaysArray($var);
}

