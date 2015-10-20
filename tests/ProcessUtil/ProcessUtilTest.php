<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 02.10.15
 * Time: 08:45
 */

namespace ProcessUtil;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class ProcessUtilTest extends \PHPUnit_Framework_TestCase
{
    public function getProcessUtil()
    {
        return ProcessUtil::instance();
    }

    public function mockProcessBuilder()
    {
        return $this->getMock(ProcessBuilder::class);
    }

    public function mockProcess()
    {
        return $this->getMock(Process::class, [], [] , '', false);
    }

    protected function mock($successfully = true)
    {
        $mock = $this->mockProcessBuilder();
        $processMock = $this->mockProcess();
        $processMock->expects($this->any())
            ->method('isSuccessful')->willReturn($successfully);
        $processMock->expects($this->any())
            ->method('run');

        $mock->expects($this->any())->method('getProcess')->willReturn($processMock);

        return $mock;
    }

    public function testSetProcessBuilder()
    {
        $mock = $this->mockProcessBuilder();
        $processUtil = $this->getProcessUtil();
        $processUtil->setProcessBuilder($mock);
        $this->assertSame($mock, $processUtil->getProcessBuilder());
    }

    public function testCommandExecution()
    {
        $mock = $this->mock();
        $processUtil = $this->getProcessUtil();
        $processUtil->setProcessBuilder($mock);
        $process = $processUtil->executeCommand(['command']);
        $this->assertEquals(true, $process->isSuccessful());
    }

    public function testCommandNotSuccessful()
    {
        $this->setExpectedException(ProcessFailedException::class);

        $mock = $this->mock(false);
        $processUtil = $this->getProcessUtil();
        $processUtil->setProcessBuilder($mock);
        $processUtil->executeCommand(['command']);
    }

    public function testProcessBuilderCallback()
    {
        $test = $this;

        $mock = $this->mock();
        $processUtil = $this->getProcessUtil();
        $processUtil->setProcessBuilder($mock);
        $processUtil->executeCommand(['command'], function ($processBuilder) use ($mock, $test) {
            $test->assertEquals($mock, $processBuilder);
        });
    }
}
