<?php
/**
 * メッセージ関連
 *
 * @filename Mes.php
 * @category MyLib
 * @package  MyLibMes
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * メッセージ関連定数定義クラス
 *
 * メッセージ内容がわかるように、意味のない通し番号等は使わない
 *
 * エラー種類：
 *  1.バリデーション時エラー（ユーザ要因）
 *  2.DB接続失敗等（システム要因）
 *  3.課金失敗等（ユーザ要因／システム要因）
 *  ※ユーザ要因は、再入力により即時エラー回避可。
 *    一方、システム要因は対策後にエラー回避可。
 *    その他、ユーザ側に見せるべきエラー内容と、見せるべきではないエラー内容がある。
 *    例：ログインエラー時に、IDエラーかパスワードエラーか詳細を表示しすぎるのはセキュリティ上問題がある。
 */
class Mes
{
    // 共通、汎用エラーメッセージ
    const ERR_USR_INPUT = '入力エラーです。';
    const ERR_USR_PARAM = 'パラメータエラーです。';
    const ERR_COM_SOMETHING = '予期せぬエラー発生';

    // システム要因エラーメッセージ
    const ERR_SYS_DB_CONN = 'データベース接続エラー';

    // ユーザ要因エラーメッセージ
    const ERR_USR_NOT_EMPTY = 'データが空ではありません。';
    const ERR_USR_NOT_INT = '数値が不正です。';
    const ERR_USR_NOT_NUM = '数字が不正です。';
    const ERR_USR_NOT_EMAIL = 'メールアドレスが不正です。';
    const ERR_USR_NOT_USERID = 'ユーザIDが不正です。';

    // 情報系メッセージ
    const INFO_WAIT = 'しばらくお待ちください。';
    const INFO_SELECT = '選択してください。';

    /**
     * コード番号から表示用メッセージを返却
     * @param mixed $code コード番号
     * @return string
     */
    public static function getMessage($code)
    {
    }
}
