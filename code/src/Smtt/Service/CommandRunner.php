<?php
namespace Smtt\Service;

class CommandRunner
{
    /** @var string */
    protected $command;

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * Execute shell command with given args
     * @return string
     */
    public function run()
    {
        $params = array_map('escapeshellarg', func_get_args());
        return shell_exec($this->command .' '. implode(' ', $params));
    }
}
