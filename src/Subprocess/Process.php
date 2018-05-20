<?php
declare(strict_types = 1);

namespace Subprocess;

/**
 * A handle to a running or previously running process.
 */
final class Process
{
    /** @var resource */
    private $proc;

    /**
     * Construct a process handle using a primitive process handle as returned
     * by \proc_open.
     *
     * Does not throw exceptions.
     *
     * @param resource $proc The process handle.
     */
    public function __construct($proc)
    {
        $this->proc = $proc;
    }

    /**
     * Wait for the process to terminate.
     *
     * Does not throw exceptions.
     */
    public function wait(): void
    {
        \proc_close($this->proc);
    }
}
