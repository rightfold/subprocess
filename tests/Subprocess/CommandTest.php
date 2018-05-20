<?php
declare(strict_types = 1);

namespace Subprocess;

use Eris;
use Eris\Generator\StringGenerator;
use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    use Eris\TestTrait;

    public function __construct()
    {
        parent::__construct();
        $this->backupGlobals = \FALSE;
        $this->backupStaticAttributes = \FALSE;
        $this->runTestInSeparateProcess = \FALSE;
    }

    public function testFromShellCommand(): void
    {
        $this->forAll(new StringGenerator())->then(
            function(string $shellCommand): void
            {
                $command = Command::fromShellCommand($shellCommand);
                $this->assertSame($command->getShellCommand(), $shellCommand);
            }
        );
    }

    public function testFromProgramNameAndArgumentsEmptyCommand(): void
    {
        $command = Command::fromProgramNameAndArguments('', ['hello', 'world']);
        $shellCommand = "'' 'hello' 'world'";
        $this->assertSame($command->getShellCommand(), $shellCommand);
    }

    public function testFromProgramNameAndArgumentsNoArguments(): void
    {
        $command = Command::fromProgramNameAndArguments('echo', []);
        $shellCommand = "'echo'";
        $this->assertSame($command->getShellCommand(), $shellCommand);
    }

    public function testFromProgramNameAndArgumentsNoSpecial(): void
    {
        $command = Command::fromProgramNameAndArguments('echo', ['hello', 'world']);
        $shellCommand = "'echo' 'hello' 'world'";
        $this->assertSame($command->getShellCommand(), $shellCommand);
    }

    public function testFromProgramNameAndArgumentsSpaces(): void
    {
        $command = Command::fromProgramNameAndArguments('echo', ['hel lo', 'world']);
        $shellCommand = "'echo' 'hel lo' 'world'";
        $this->assertSame($command->getShellCommand(), $shellCommand);
    }

    public function testFromProgramNameAndArgumentsDollarSigns(): void
    {
        $command = Command::fromProgramNameAndArguments('echo', ['hel$lo', 'world']);
        $shellCommand = "'echo' 'hel\$lo' 'world'";
        $this->assertSame($command->getShellCommand(), $shellCommand);
    }

    public function testFromProgramNameAndArgumentsSingleQuotes(): void
    {
        $command = Command::fromProgramNameAndArguments('echo', ["hel'lo", "wor''ld"]);
        $shellCommand = "'echo' 'hel'\''lo' 'wor'\'''\''ld'";
        $this->assertSame($command->getShellCommand(), $shellCommand);
    }
}
