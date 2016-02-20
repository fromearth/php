<?php
/**
 * 認証・アクセスコントロール関連
 *
 * @filename Auth.php
 * @category MyLib
 * @package  MyLibAuth
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * 認証・アクセスコントロール関連クラス
 */
class Auth
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // セッション開始前処理
        session_name("SID");

        // セッション開始
        session_start();
    }

    /**
     * ユーザ認証およびアクセス制御
     * @return void
     */
    public function identify()
    {
        // 認証
        if (!empty($_SESSION['id'])) {
            // セッションにユーザIDがある場合
            $stat = $_SESSION['stat'];
        } else if (isset($_POST['cmd']) && $_POST['cmd'] === "login") {
            // ログイン処理がリクエストされてきている場合
            if (isset($_POST['id'], $_POST['pass'])) {
                // idとpassが送信されてきている
                $stat = $this->auth($_POST['id'], $_POST['pass']);
            } else {
                // idとpassが送信されてきていない
            }
        } else {
            // 不明リクエスト
        }

        if (empty($stat)) {
            // ステータス取得できなかった場合
            return $this->logout("/login");  // ログイン画面へ飛ばす
        }

        // ステータスにより振り分け
        switch ($stat) {
        case "1":
            // 認証できた / ステータス = 承認前(仮登録)
            return $this->redirect("/login/wait");
        case "2":
            // 認証できた / ステータス = 承認済み
            return;
        case "3":
            // 認証できた / ステータス = アカウント停止中
            return $this->redirect("/login/stop");
        default:
            // 認証に失敗
            return $this->logout("/login");
        }
    }

    /**
     * IDとパス照合判定のみを行う
     * @param string $id   ユーザID
     * @param string $pass パスワード
     * @return bool 判定結果
     */
    private function auth($id, $pass)
    {
        // IDが一致するレコード取得
        $idInfo = $this->getIdInfo($id);
        if (empty($idInfo)) {
            // 該当ID情報がなかった場合
            return false;
        }

        return $idInfo['pass_hashed'] === $this->hashPass($id, $pass) ? $idInfo['stat'] : null;
    }

    /**
     * ログアウト処理
     * @param string [$url=null] ログアウト後の飛び先URL
     * @return void
     */
    public function logout($url=null)
    {
        $_SESSION = array();  // セッション変数を消す
        session_destroy();    // セッション破棄

        if (!is_null($url)) {
            // ログアウト後に飛ばす先が指定されていなければ
            $url = "/";   // トップページ
        }

        $this->redirect$url);
    }

    /**
     * リダイレクト
     * @param string $url 飛ばす先
     * @return void
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * ユーザIDに紐づく情報を取得
     * @param string $id ユーザID
     * @return array ユーザ情報
     */
    private function getIdInfo($id)
    {
        return $_SESSION['data'];
    }

    /**
     * パスワードのハッシュ化
     * 他ユーザの同じパスワードであっても異なるハッシュになるようIDも加えてハッシュ化
     * @param string $id   ユーザID
     * @param string $pass パスワード
     * @return string ハッシュ化されたパスワード
     */
    private function hashPass($id, $pass)
    {
        $hashedWithLen = strlen($pass . $id) . md5($pass . $id);

        return md5($hashedWithLen);  // 解析されにくくなるようmd5多重化
    }

    /**
     * ユーザがリクエストしたURLに対しアクセス可能かどうか判定
     * @param string $url リクエストURL
     * @return bool 判定値
     */
    private function canAccess($url)
    {
        $role = Mst::getRole();
        $rsc = Mst::getResource();
    }

    /**
     * 役割を定義
     */
    private function setRole()
    {
    }
}
