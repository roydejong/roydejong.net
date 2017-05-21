<?php

namespace roydejong\dotnet\Integration\Structs;

/**
 * Data struct for a sleep activity (fitbit).
 */
class SleepSummary
{
    /**
     * Date of sleep YYYY-MM-DD.
     *
     * @var string
     */
    public $dateOfSleep;

    /**
     * Duration of the sleep in seconds.
     *
     * @var int
     */
    public $durationSecs;

    /**
     * Percentage (0 - 100) rating of sleep efficiency.
     *
     * @var int
     */
    public $efficiency;

    /**
     * Date/time on which sleep ended.
     *
     * @var \DateTime
     */
    public $endDateTime;

    /**
     * Date/time on which sleep started.
     *
     * @var \DateTime
     */
    public $startDateTime;

    /**
     * @var int
     */
    public $minutesInBed;

    /**
     * @var int
     */
    public $minutesAsleep;
}