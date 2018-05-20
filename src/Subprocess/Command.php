<?php
declare(strict_types = 1);

namespace Subprocess;

/**
 * A command describes what a process that will be executed will look like.
 */
final class Command
{
    /** @var string
     *
     * Private because at some point PHP may get a decent process spawning
     * facility so we could move away from shell commands.
     */
    private $shellCommand;

    /** @var ?string Set to non-NULL when the working directory of the spawned
     *               process should be different from that of the spawning
     *               process. */
    public $workingDirectory;

    /** @var ?array<string,string> Set to non-NULL when the environment of the
     *                             spawned process should be different from
     *                             that of the spawning process. */
    public $environment;

    private function __construct(string $shellCommand)
    {
        $this->shellCommand = $shellCommand;
        $this->workingDirectory = \NULL;
        $this->environment = \NULL;
    }

    /**
     * Create a new command from a shell command, which will be passed to
     * /bin/sh.
     *
     * Does not throw exceptions.
     *
     * @param string $shellCommand The shell command to be passed to /bin/sh.
     * @return Command The command.
     */
    public static function fromShellCommand(string $shellCommand): Command
    {
        return new self($shellCommand);
    }

    /**
     * Create a new command given the name of a program and an array of
     * arguments to pass to the program.
     *
     * When the name of the program contains slashes, it is interpreted as an
     * absolute or relative path to the executable. Otherwise, the executable
     * is searched for in PATH.
     *
     * The program will receive the program name as the zeroth argument.
     *
     * Does not throw exceptions.
     *
     * @param string $programName The program name.
     * @param array<string> $arguments The program arguments.
     * @return Command The command.
     */
    public static function fromProgramNameAndArguments(string $programName, array $arguments): Command
    {
        $shellCommand = \escapeshellarg($programName);
        foreach ($arguments as $argument)
        {
            $shellCommand .= ' ' . \escapeshellarg($argument);
        }
        return self::fromShellCommand($shellCommand);
    }

    /**
     * Return the shell command that would be executed when executing this
     * command.
     *
     * Does not throw exceptions.
     *
     * @return string The shell command.
     */
    public function getShellCommand(): string
    {
        return $this->shellCommand;
    }

    /**
     * Execute the command, returning a process handle. You may call this
     * method multiple times to spawn similar processes. The processes
     * themselves will not affect the Command object.
     *
     * @throws SpawnException If the process could not be executed.
     *
     * @return Process The spawned process.
     */
    public function spawn(): Process
    {
        $descriptorspec = [];
        $cwd = $this->workingDirectory;
        $env = $this->environment;
        $other_options = [];
        $proc = \proc_open(
            $this->shellCommand,
            $descriptorspec,
            $pipes,
            $this->workingDirectory,
            $this->environment,
            $other_options
        );
        return new Process($proc);
    }
}
