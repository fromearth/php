<?php
/**
 * カレンダー作成処理
 *
 * @filename Calendar.php
 * @category MyLib
 * @package  MyLibCalendar
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * カレンダー作成処理クラス
 *
 */
class Calendar
{
    // 祝祭日（固定日）
    private static $hList = array(
        //月 => array(日 => '祝祭日名'),
        1 => array( 1 => '元日'),
        2 => array(11 => '建国記念の日'),
        4 => array(29 => '昭和の日'),
        5 => array( 3 => '憲法記念日',
                    4 => 'みどりの日',
                    5 => 'こどもの日'),
        11 => array( 3 => '文化の日',
                    23 => '勤労感謝の日'),
        12 => array(23 => '天皇誕生日'),
    );

    // 祝祭日（変動日 / ハッピーマンデー）
    private static $mList = array(
        //月 => array(第N週目 => '祝祭日名'),
        1 => array(2 => '成人の日'),
        2 => array(3 => '海の日'),
        9 => array(3 => '敬老の日'),
        10 => array(2 => '体育の日'),
    );

    /**
     * カレンダー作成
     * @param int [$year=null] 年
     * @param int [$mon=null]  月
     * @return string カレンダー
     * @example CSS
     * <style>
     * .calendar,
     * .calendar th,
     * .calendar td {
     *   border-collapse: collapse;
     *   border: 1px solid black;
     *   background-color: White;
     *   color: Black;
     *   text-align: center;
     * }
     * .calendar .today {
     *   background-color: Gold;
     *   font-weight: bold;
     * }
     * .calendar .holiday,
     * .calendar .sun {
     *   background-color: MistyRose;
     *   color: Red;
     * }
     * .calendar .sat {
     *   background-color: Lavender;
     *   color: Blue;
     * }
     * </style>
     */
    public static function render($year=null, $mon=null)
    {
        // 属性
        $table     = ' class="calendar"';
        $tdToday   = ' class="today"';
        $tdHoliday = ' class="holiday"';
        $tdSun     = ' class="sun"';
        $tdSat     = ' class="sat"';

        // カレンダー情報
        $list = self::getCalendarInfo($year, $mon);

        // 成形
        $cal = "<table{$table}>\n";

        $cal .= "<tr>\n";
        foreach (Date::$wdayList as $wday => $text) { // ヘッダ
            switch ($wday) {
            case Date::WDAY_SUN:
                $cal .= "\t<th{$tdSun}>{$text}</th>\n";
                continue;
            case Date::WDAY_SAT:
                $cal .= "\t<th{$tdSat}>{$text}</th>\n";
                continue;
            default:
                $cal .= "\t<th>{$text}</th>\n";
                continue;
            }
        }
        $cal .= "</tr>\n";

        foreach ($list as $day => $data) {
            if ($day == 1) {
                // 月初めの場合
                $cal .= "<tr>\n"; // 行開始
                $cal .= str_repeat("\t<td></td>\n", $data['wday']); // 足りないセル補完
            } else if ($data['wday'] == Date::WDAY_SUN) {
                // 日曜の場合
                $cal .= "<tr>\n"; // 行開始
            }

            // 付加属性の整理
            $tdClass = '';
            $tdTitle = '';
            if (isset($data['today'])) {
                $tdClass = $tdToday;
                if (isset($data['holiday'])) {
                    $tdTitle = ' title="本日 ' . $data['holiday'] . '"';
                } else {
                    $tdTitle = ' title="本日"';
                }
            } else if (isset($data['holiday'])) {
                $tdClass = $tdHoliday;
                $tdTitle = ' title="' . $data['holiday'] . '"';
            } else if ($data['wday'] == Date::WDAY_SUN) {
                $tdClass = $tdSun;
            } else if ($data['wday'] == Date::WDAY_SAT) {
                $tdClass = $tdSat;
            }

            // 日付セル
            $cal .= "\t<td{$tdClass}{$tdTitle}>{$day}</td>\n";

            if ($data['wday'] == Date::WDAY_SAT) {
                // 土曜の場合
                $cal .= "</tr>\n"; // 行終了
            }

            $lastWday = $data['wday'];
        }

        // セル補完
        $cal .= str_repeat("\t<td></td>\n", 6 - $lastWday);

        $cal .= "</table>";

        return $cal;
    }

    /**
     * 指定年月のカレンダー情報
     * @param int [$year=null] 年
     * @param int [$mon=null]  月
     * @return array 月情報配列
     */
    public static function getCalendarInfo($year=null, $mon=null)
    {
        $list = array();

        // 年月の汚染があれば除去
        Date::cleanDate($year, $mon);

        // 月初めの曜日
        $w = Date::getWeekNumberOfFirstDay($year, $mon);

        // 月の日数
        $lastDay = Date::getLastDay($year, $mon);

        // 日付情報
        for ($day=1; $day<=$lastDay; $day++) {
            $holi = self::isHoliday($year, $mon, $day);
            if ($holi !== false) {
                $list[$day]['holiday'] = $holi;
            }

            $today = Date::isToday($year, $mon, $day);
            if ($today === true) {
                $list[$day]['today'] = "本日";
            }

            $list[$day]['wday'] = $w;
            if (++$w > Date::WDAY_SAT) {
                $w = Date::WDAY_SUN;
            }
        }

        return $list;
    }

    /**
     * 入力年月日が祝祭日かどうか判定
     * @param int $year 年
     * @param int $mon  月
     * @param int $day  日
     * @return mixed "祝祭日名" or false
     */
    public static function isHoliday($year, $mon, $day)
    {
        // 祝日
        $result = self::_isHoliday($year, $mon, $day);
        if ($result !== false) {
            return $result;
        }

        // 振替休日（祝日が日曜と重なるケース）
        $di = Date::getDateInfo(Date::mkTime($year, $mon, $day));
        if ($di['wday'] != Date::WDAY_SUN) {
            for ($i=1;; $i++) {
                $d = Date::moveDate($year, $mon, $day, -$i);
                $holi = self::_isHoliday($d['year'], $d['mon'], $d['mday']);
                if ($holi !== false && $d['wday'] == Date::WDAY_SUN) {
                    return '振替休日';
                } else if ($holi === false && $d['wday'] != Date::WDAY_SUN) {
                    break;
                }
            }
        }

        // 国民の休日（祝日と祝日に挟まれた日）
        $pd = Date::prevDate($year, $mon, $day);
        $nd = Date::nextDate($year, $mon, $day);
        if (self::_isHoliday($pd['year'], $pd['mon'], $pd['mday'])
                && self::_isHoliday($nd['year'], $nd['mon'], $nd['mday'])) {
            // 前日および翌日が祝祭日の場合
            return '国民の休日';
        }

        return false;
    }

    /**
     * 入力年月日が祝祭日かどうか判定（固定日および変動日）
     * @param int $year 年
     * @param int $mon  月
     * @param int $day  日
     * @return mixed "祝祭日名" or false
     */
    private static function _isHoliday($year, $mon, $day)
    {
        // 固定日
        if (isset(self::$hList[$mon][$day])) {
            return self::$hList[$mon][$day];
        }

        // 変動日（ハッピーマンデー）
        if (isset(self::$mList[$mon])) {
            foreach (self::$mList[$mon] as $nth => $name) {
                if (self::getNthDay($year, $mon, $nth, Date::WDAY_MON) == $day) {
                    return $name;
                }
            }
        }

        // 変動日（春分の日）
        if ($mon == 3 && $day == intval(20.8431 + 0.242194 *
                            ($year - 1980) - intval(($year - 1980) / 4))) {
            return '春分の日';
        }

        // 変動日（秋分の日）
        if ($mon == 9 && $day == intval(23.2488 + 0.242194 *
                            ($year - 1980) - intval(($year - 1980) / 4))) {
            return '秋分の日';
        }

        return false;
    }

    /**
     * 第N番目の曜日に該当する日付を返却
     * @param int $year 年
     * @param int $mon  月
     * @param int $nth  週番目
     * @param int $wday 曜日番号
     * @return int 日付
     */
    private static function getNthDay($year, $mon, $nth, $wday)
    {
        // 月初日の曜日
        $w1 = Date::getWeekNumberOfFirstDay($year, $mon);

        // 最初に来る$wday曜日までの日数
        if ($w1 <= $wday) {
            $days = 1 + ($wday - $w1);
        } else {
            $days = 1 + ($wday - $w1) + 7;
        }

        return $days + (7 * ($nth - 1));
    }
}
