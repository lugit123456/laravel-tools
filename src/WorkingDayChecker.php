<?php

namespace LaravelTools\WorkingDayChecker;

use Carbon\Carbon;
use LaravelTools\WorkingDayChecker\Exceptions\WorkingDayCheckerException;

class WorkingDayChecker
{
    /**
     * Check if the given date is a working day.
     *
     * @param string $date
     * @return bool
     */
    public function isWorkingDay($date)
    {
        $this->isValidDate($date);

        $getAllDates = $this->getAllDates();

        if (!isset($getAllDates[$date])) {
            throw new WorkingDayCheckerException($date . '不在日期范围内'); // 抛出异常
        }
        if (isset($getAllDates[$date]['workday']) && $getAllDates[$date]['workday'] === 1) {
            return true;
        } elseif (isset($getAllDates[$date]['workday']) && $getAllDates[$date]['workday'] === 0) {
            return false;
        }
    }

    /**
     * 获取下个工作日，也可以获取指定日期前后几天的工作日
     * @param $date
     * @param $dayNum
     * @return mixed
     * @throws WorkingDayCheckerException
     */
    public function getNextWorkingDay($date, $dayNum = 1)
    {
        // 这里实现获取下一个工作日的逻辑
        $this->isValidDate($date);

        $getAllDates = $this->getAllDates();

        $dateTime = strtotime($date);
        $year = date('Y', $dateTime);
        $month = date('m', $dateTime);
        $day = date('d', $dateTime);
        $getHoliday = getHoliday();

        $dates = [];
        for ($i = 1; $i <= 100; $i++) {
            if (count($dates) >= abs($dayNum)) {
                break;
            }
            $date = Carbon::create($year, $month, $day, 1, 0, 0);
            if ($dayNum > 0) {
                $future = $date->addDays($i);
            } else {
                $future = $date->subDays($i);
            }

            $futureDate = $future->format('Ymd');

            if (isset($getHoliday[$futureDate]['workday']) && $getHoliday[$futureDate]['workday'] === 1) {
                $dates[] = $future->format('Y-m-d');
            }
        }

        return end($dates);
    }

    /**
     * Get the previous working day before the given date.
     *
     * @param string $date
     * @return string
     */
    public function getPreviousWorkingDay($date)
    {
        // 这里实现获取上一个工作日的逻辑
    }

    public function getAllDates()
    {
        $current_year_file_path = $_SERVER['DOCUMENT_ROOT'] . 'holiday.txt';
        $next_year_file_path = $_SERVER['DOCUMENT_ROOT'] . 'holiday_next.txt';
        if (date('m') === '03' && file_exists($next_year_file_path)) {
            // 删除 holiday.txt 文件 ，3月份的时候应该不会再用到去年的数据了，则把去年的数据删除，重命名holiday_next的文件为holiday
            @unlink($current_year_file_path);
            // 重命名 holiday.txt 为 holiday.txt
            rename($next_year_file_path, $current_year_file_path);
        }

        if (!file_exists($current_year_file_path)) {
            $holidays = file_get_contents('https://api.apihubs.cn/holiday/get?size=500&year=' . date('Y'));
            $holidays = json_decode($holidays, true);
            if (!$holidays['code'] && $holidays['data']['list']) {
                file_put_contents($current_year_file_path, json_encode($holidays, 320));
            }
        }

        if (date('m') === 12 && !file_exists($next_year_file_path)) {
            $nextYearHolidays = file_get_contents('https://api.apihubs.cn/holiday/get?size=500&year=' . (date('Y') + 1));
            $nextYearHolidays = json_decode($nextYearHolidays, true);
            if (!$nextYearHolidays['code'] && $nextYearHolidays['data']['list']) {
                file_put_contents($next_year_file_path, json_encode($nextYearHolidays, 320));
            }
        }

        $current_year_holidays = [];
        // 上面已经写入好文件内容，那么下面就进行读取文件内容
        if (file_exists($current_year_file_path)) {
            $current_year_holidays = json_decode(file_get_contents($current_year_file_path), true)['data']['list'];

        }
        $next_year_holidays = [];
        // 上面已经写入好文件内容，那么下面就进行读取文件内容
        if (file_exists($next_year_file_path)) {
            $next_year_holidays = json_decode(file_get_contents($next_year_file_path), true)['data']['list'];
        }

        $date = array_merge($current_year_holidays, $next_year_holidays);
        return array_column($date, null, 'date');
    }

    public function isValidDate($dateString)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $dateString);
        if (!$date || $date->format('Y-m-d') !== $dateString) {
            throw new WorkingDayCheckerException('日期格式有误！'); // 抛出异常
        }
    }
}
