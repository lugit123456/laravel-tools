<?php

namespace LaravelTools\WorkingDayChecker;

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
        return false;
        // 这里实现判断逻辑
    }

    /**
     * Get the next working day after the given date.
     *
     * @param string $date
     * @return string
     */
    public function getNextWorkingDay($date)
    {
        // 这里实现获取下一个工作日的逻辑
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
}
