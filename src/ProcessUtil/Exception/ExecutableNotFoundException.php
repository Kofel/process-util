<?php

namespace ProcessUtil\Exception;


use Symfony\Component\Process\Process;

class ExecutableNotFoundException extends \RuntimeException
{
    private $process;

    /**
     * ExecutableNotFoundException constructor.
     * @param $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
        parent::__construct(sprintf('Executable "%s" not found', $process->getCommandLine()));
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }
}