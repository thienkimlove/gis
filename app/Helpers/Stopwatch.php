<?php
namespace Gis\Helpers;

class Stopwatch
{
    private $startStamp;
    private $runTime;
    private $started;
    private $stopped;
    private $paused;
    private $countdownDuration;
    /**
     * @param $startNow bool
     * @param $countdown float Duration to countdown from in milliseconds.
     */
    function __construct( $startNow = true, $countdown = null )
    {
        if ( $startNow )
            $this->start( $countdown );
    }
    /**
     * Starts and resets the timer.
     * Optionally accepts a duration to countdown from.
     *
     * @param $countdown float Duration to countdown from in milliseconds.
     */
    public function start( $countdown = 0 )
    {
        $this->startStamp = microtime(true);
        $this->runTime = 0;
        $this->countdownDuration = $countdown;
        $this->started = true;
        $this->stopped = false;
        $this->paused = false;
    }
    /**
     * Pauses timing.
     * Does not unpause, like some physical stopwatches do.
     */
    public function pause()
    {
        if ( $this->isRunning() )
        {
            $this->runTime += microtime(true) - $this->startStamp;
            $this->paused = true;
        }
    }
    /**
     * If paused, will resume timing.
     */
    public function resume()
    {
        if ( $this->isPaused() )
        {
            $this->startStamp = microtime(true);
            $this->paused = false;
        }
    }
    /**
     * Stops timing and freezes elapsed and countdown.
     * Blocks resuming but not (re)starting.
     * You will rarely use this but it exists
     * both for it's functionality and to clarify pause().
     */
    public function stop()
    {
        if ( $this->started && ! $this->stopped )
        {
            if ( ! $this->paused )
                $this->runTime += microtime(true) - $this->startStamp;
            $this->stopped = true;
            $this->paused = false;
        }
    }
    /**
     * Allows elapsed to be explicitly changed after starting.
     * This is generally intended to be used while the stopwatch is running/paused
     * as start() will reset elapsed.
     *
     * @param $value float Duration in milliseconds
     */
    public function setElapsed( $value )
    {
        if ( $this->stopped )
            return;
        $this->runTime = $value;
        if ( $this->isRunning() )
            $this->startStamp = microtime(true);
    }
    /**
     * Allows countdown to be changed after starting.
     * This is generally intended to be used while the stopwatch is running
     * as start() will reset countdown.
     *
     * @param $value float Duration in millis (i.e. Lil Wanye's)
     */
    public function setCountdown( $value )
    {
        if ( $this->stopped )
            return;
        $this->countdownDuration = $value;
    }
    /**
     * @return float Duration in milliseconds since start() or setElapsed()
     */
    public function getElapsed()
    {
        if ( $this->isRunning() )
            return ( microtime(true) - $this->startStamp ) + $this->runTime;
        return $this->runTime;
    }
    /**
     * @return float The remaining countdown in milliseconds (will return negative values)
     */
    public function getRemaining()
    {
        $remain = $this->countdownDuration - $this->getElapsed();
        return $remain;
    }
    /**
     * @return bool Whether countdown is done.
     */
    public function isDone()
    {
        return ( $this->getRemaining() <= 0 );
    }
    /**
     * @return State of running.
     */
    public function isRunning()
    {
        if ( $this->started && ! $this->paused && ! $this->stopped )
            return true;
        return false;
    }
    /**
     * @return State of paused.
     */
    public function isPaused()
    {
        if ( $this->started && $this->paused )
            return true;
        return false;
    }
    /**
     * @return State of stopped.
     */
    public function isStopped()
    {
        if ( $this->started && $this->stopped )
            return true;
        return false;
    }
    /* Why not? Quicker debugging, right?! */
    function __toString()
    {
        return 'Stopwatch elapsed ' . $this->getElapsed() . ' milliseconds, remaining ' . $this->getRemaining() . ' milliseconds';
    }
}
