<?php
/**
 * 日付・時間関連
 *
 * @filename Date.php
 * @category MyLib
 * @package  MyLibDate
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');


// 定数定義
define("SECONDS_A_DAY", 60 * 60 * 24); // 1日の秒数
define("MICROTIME", microtime(true));  // クラス呼出し時マイクロ秒


/**
 * 日付・時間関連を扱うクラス
 *
 */
class Date
{
    // 定数
    const JOIN_DATE = '-';
    const JOIN_TIME = ':';
    // 曜日番号
    const WDAY_SUN = 0;
    const WDAY_MON = 1;
    const WDAY_TUE = 2;
    const WDAY_WED = 3;
    const WDAY_THU = 4;
    const WDAY_FRI = 5;
    const WDAY_SAT = 6;

    // クラス内保持
    private static $ts; // タイムスタンプをキャッシュ
    private static $dateInfo; // 日付情報をキャッシュ
    public static $wdayList = array('日', '月', '火', '水', '木', '金', '土');

    /**
     * 実行にかかった秒数を取得
     * @return float 実行秒
     */
    public static function loadTime()
    {
        return sprintf('%.3f', microtime(true) - MICROTIME);
    }

    /**
     * 現在のタイムスタンプ取得
     * @param bool [$fresh=false] 最新取得フラグ
     * @return int タイムスタンプ
     */
    public static function getTimestamp($fresh=false)
    {
        if ($fresh) {
            self::$ts = time();
        } else if (empty(self::$ts)) {
            self::$ts = (int)MICROTIME;
        }

        return self::$ts;
    }

    /**
     * 最新のタイムスタンプ取得
     * @return int タイムスタンプ
     */
    public static function getFreshTimestamp()
    {
        return self::getTimestamp(true);
    }

    /**
     * 指定タイムスタンプの日付情報一式
     * @param int [$ts=null] タイムスタンプ
     * @return array 日付情報
     */
    public static function getDateInfo($ts=null)
    {
        if (is_null($ts)) {
            // 引数がなければ
            $ts = self::getTimestamp(); // タイムスタンプ取得
        }

        if (empty(self::$dateInfo[$ts])) {
            // 日付情報キャッシュなければ
            self::$dateInfo[$ts] = getdate($ts);

            $di = &self::$dateInfo[$ts]; // エイリアス（短縮記法）

            // 汎用データ付加（システムで多用する日付形式を用意）
            $di['0mon'    ] = substr('0'.$di['mon'    ], -2);
            $di['0mday'   ] = substr('0'.$di['mday'   ], -2);
            $di['0hours'  ] = substr('0'.$di['hours'  ], -2);
            $di['0minutes'] = substr('0'.$di['minutes'], -2);
            $di['0seconds'] = substr('0'.$di['seconds'], -2);
            $di['wday_wa' ] = self::$wdayList[$di['wday']];
            $di['date'  ] = $di['year']
                          . $di['0mon'] . $di['0mday'];        // YYYYMMDD
            $di['time'  ] = $di['0hours']
                          . $di['0minutes'] . $di['0seconds']; // HHMISS
            $di['ymd'   ] = $di['year'  ]
                          . self::JOIN_DATE . $di['0mon' ]
                          . self::JOIN_DATE . $di['0mday'];   // YYYY-MM-DD
            $di['ymd_s' ] = $di['year'  ]
                          . self::JOIN_DATE . $di['mon' ]
                          . self::JOIN_DATE . $di['mday'];    // YYYY-M-D
            $di['ymd_wa'] = $di['year'  ]
                          . "年" . $di['mon' ]
                          . "月" . $di['mday'] . "日";        // YYYY年M月D日
            $di['his'   ] = $di['0hours']
                          . self::JOIN_TIME . $di['0minutes']
                          . self::JOIN_TIME . $di['0seconds']; // HH:MI:SS
            $di['hi_s'  ] = $di['hours' ]
                          . self::JOIN_TIME . $di['0minutes']; // H:MI
        }

        return self::$dateInfo[$ts];
    }

    /**
     * 指定日付をタイムスタンプに変換
     * @param string $date 日付文字列
     * @return int タイムスタンプ
     */
    public static function strToTime($date)
    {
        if (preg_match('/[\x80-\xff]/', $date) === 1) {
            // 「年」「月」などマルチバイト文字があれば処理できるよう変換
            $date = mb_convert_encoding($date, "UTF-8", "JIS,UTF-8,CP932,EUC-JP");
            $date = mb_convert_kana($date, "as", "UTF-8");
            $date = preg_replace('/[\x80-\xff]/', ' ', $date);
            $date = preg_replace('/\b(\d{4})\D+(0?\d|1[0-2])\D+([0-2]?\d|3[01])\b/', '\1-\2-\3', $date);
            $date = preg_replace('/ ([01]?\d|2[0-3])[^\d\-:]+([0-5]?\d)[^\d\-:]+([0-5]?\d)\b/', ' \1:\2:\3', $date);
            $date = preg_replace('/ ([01]?\d|2[0-3])[^\d\-:]+([0-5]?\d)\b/', ' \1:\2', $date);
        }

        try {
            if (class_exists('DateTime')) {
                // DateTimeクラスが使える場合
                $ts = new DateTime($date);

                return $ts->format('U');
            }

            return strtotime($date);
        } catch (Exception $e) {
            // 例外発生
            return 0;
        }
    }

    /**
     * 指定日付が存在するか判定
     * @param mixed $date       日付文字列 or 年
     * @param int   [$mon=null] 月
     * @param int   [$day=null] 日
     * @return bool 判定値
     */
    public static function checkDate($date, $mon=null, $day=null)
    {
        if (is_null($mon)) {
            // 第2引数指定なければ
            $ts = self::strToTime($date); // タイムスタンプに変換

            if ($ts === false) return false; // 無効な日付ならfalse返却

            // 日付情報を取得し、年月日をセット
            $di = self::getDateInfo($ts);
            $date = $di['year'];
            $mon  = $di['mon' ];
            $day  = $di['mday'];
        } else if (is_null($day)) {
            // 第3引数指定なければ
            $day = 1; // 判定用に、必ず存在する日にち1をセット
        } else {
            // 年月日指定の場合
        }

        return checkdate((int)$mon, (int)$day, (int)$date);
    }

    /**
     * 入力年月日の洗浄
     * 指定年月[日]が存在しなければ実行日をセット
     * @param int $year       年
     * @param int $mon        月
     * @param int [$day=null] 日
     * @return void
     */
    public static function cleanDate(&$year, &$mon, &$day=null)
    {
        // 汚染を除去 "09月" => 9
        $year = (int)$year;
        $mon  = (int)$mon;
        $_day = (int)$day;

        if (empty($_day)) $_day = 1; // 日にち指定なければ必ず存在する1セット

        if (!self::checkDate($year, $mon, $_day)) {
            // 存在しない年月日の場合
            $di = self::getDateInfo(); // 実行日の情報取得

            $year = $di['year'];
            $mon  = $di['mon' ];
            $day  = $di['mday'];
        }
    }

    /**
     * 相対日付情報
     * @param int $year 年
     * @param int $mon  月
     * @param int $day  日
     * @param int $move 移動日数
     * @return array 日付情報
     */
    public static function moveDate($year, $mon, $day, $move)
    {
        return self::getDateInfo(self::mkTime($year, $mon, $day + $move));
    }

    /**
     * 前日情報
     * @param int $year 年
     * @param int $mon  月
     * @param int $day  日
     * @return array 日付情報
     */
    public static function prevDate($year, $mon, $day)
    {
        return self::moveDate($year, $mon, $day, -1);
    }

    /**
     * 翌日情報
     * @param int $year 年
     * @param int $mon  月
     * @param int $day  日
     * @return array 日付情報
     */
    public static function nextDate($year, $mon, $day)
    {
        return self::moveDate($year, $mon, $day, +1);
    }

    /**
     * 指定年月日をタイムスタンプに変換
     * @param int $year 年
     * @param int $mon  月
     * @param int $day  日
     * @return int タイムスタンプ
     */
    public static function mkTime($year, $mon, $day)
    {
        self::cleanDate($year, $mon, $day);

        if (class_exists('DateTime')) {
            // DateTimeクラスが使える場合
            $ts = new DateTime();
            $ts->setDate($year, $mon, $day);

            return $ts->format('U');
        }

        return mktime(0, 0, 0, $mon, $day, $year);
    }

    /**
     * 指定年月の日数
     * @param int $year 年
     * @param int $mon  月
     * @return int 日数
     */
    public static function getLastDay($year, $mon)
    {
        return (int)date('t', self::mkTime($year, $mon, 1));
    }

    /**
     * 指定年月初日の曜日番号
     * @param int $year 年
     * @param int $mon  月
     * @return int 曜日番号
     */
    public static function getWeekNumberOfFirstDay($year, $mon)
    {
        return (int)date('w', self::mkTime($year, $mon, 1));
    }

    /**
     * 日時データから時間情報を切り落として返却
     * @param string $dateTime 日時文字列データ
     * @return string YYYY-MM-DD形式文字列
     */
    public static function cutTime($dateTime)
    {
        $di = self::getDateInfo(self::strToTime($dateTime));

        return $di['ymd'];
    }

    /**
     * 誕生日から年齢計算
     * @param string $birthday 誕生日
     * @return int 年齢
     */
    public static function calcAge($birthday)
    {
        $d = self::getDateInfo();
        $b = self::getDateInfo(self::strToTime($birthday));

        return $d['year'] - $b['year'] - (int)($b['yday'] > $d['yday']);
    }

    /**
     *
     */
    public static function isToday($year, $mon, $day)
    {
        $d = self::getDateInfo();

        return $year == $d['year']
            && $mon  == $d['mon' ]
            && $day  == $d['mday'];
    }

    /**
     * 秒を日数情報に変換
     * @param int $seconds 秒
     * @return array 日数情報
     */
    public static function convSecondsToDays($seconds)
    {
        $days = $seconds / SECONDS_A_DAY; // 日数

        $rest = $seconds % SECONDS_A_DAY; // 残り秒

        $hours = $rest / (60 * 60);       // 時間

        $rest = $rest % (60 * 60);        // 残り秒

        $minutes = $rest / 60;            // 分

        $seconds = $rest % 60;            // 秒

        return array(
            "days" => $days,
            "hours" => $hours,
            "minutes" => $minutes,
            "seconds" => $seconds,
        );
    }
}
