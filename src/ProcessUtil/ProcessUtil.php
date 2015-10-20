<?php

namespace ProcessUtil;


use ProcessUtil\Exception\ExecutableNotFoundException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class ProcessUtil
{
    /** @var self */
    private static $instance;

    /**
     * @return ProcessUtil
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(new ProcessBuilder());
        }

        return self::$instance;
    }

    /** @var ProcessBuilder */
    private $processBuilder;

    /**
     * ProcessUtil constructor.
     * @param ProcessBuilder $processBuilder
     */
    protected function __construct(ProcessBuilder $processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }

    /**
     * @return ProcessBuilder
     */
    public function getProcessBuilder()
    {
        return $this->processBuilder;
    }

    /**
     * @param ProcessBuilder $processBuilder
     */
    public function setProcessBuilder($processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }

    /**
     * Executes given command
     * @param array $arguments
     * @param null $processBuilderCallback
     * @return \Symfony\Component\Process\Process
     * @throws ExecutableNotFoundException
     * @throws ProcessFailedException
     */
    public function executeCommand(array $arguments, $processBuilderCallback = null)
    {
        $processBuilder = clone $this->processBuilder;
        $processBuilder->setArguments($arguments);

        if (is_callable($processBuilderCallback)) {
            $processBuilderCallback($processBuilder);
        }

        $process = $processBuilder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            if (127 === $process->getExitCode()) {
                throw new ExecutableNotFoundException($process);
            }

            throw new ProcessFailedException($process);
        }

        return $process;
    }
}