/**
 * 配列処理
 *
 * 実践的な配列処理メモ
 *
 * @filename array.php
 * @category Examples
 * @package  ExamplesArray
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


// 配列マージの違い
$newArray = array_merge($array1, $array2);  // $array1 と $array2 で同じキー文字列があれば $array2で値が上書きされる

$newArray = $array1 + $array2;              // 配列結合演算子 + を使えば、上書きされない



// 配列要素追加の違い
array_push($array, $add);  // 関数を使う方法

$array[] = $add;           // 関数を使わない分だけ、関数呼び出しオーバーヘッドがない
