<?php
/**
 * HTML関連処理
 *
 * @filename Html.php
 * @category MyLib
 * @package  MyLibHtml
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * HTML関連処理クラス
 *
 * @see Mst.php
 */
class Html
{
    /**
     * リストボックス作成
     * @param array  $mst  マスタ配列
     * @param string $name 要素名
     * @param int    $id   初期値
     * @return string タグ文字列
     */
    public static function makeListBox(&$mst, $name, $id=null)
    {
        $listBox = '<select name="' . $name . '">';

        if (is_null($id)) {
            // 初期値指定なければ
            $listBox .= '<option value="">選択してください</option>';
        }

        foreach ($mst as $data) {
            if ($id == $data['id']) {
                $listBox .= '<option value="' . $id . '" selected="selected">';
            } else {
                $listBox .= '<option value="' . $data['id'] . '">';
            }
            $listBox .= $data['value'] . '</option>';
        }
        $listBox .= '</select>';

        return $listBox;
    }

    /**
     * タグを無害化 / サニタイズ
     * @param string $s           入力文字列
     * @param string [$enc=UTF-8] 文字エンコーディング
     * @return string
     */
    function h($s, $enc='UTF-8')
    {
        return htmlspecialchars($s, ENT_QUOTES, $enc);
    }
}
