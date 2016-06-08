<?php

abstract class Command
{
    protected $deps;
    protected $flags = array();

    public function setDeps(Deps $deps)
    {
        $this->deps = $deps;
    }

    abstract public function getName();
    abstract public function getDescription();
    abstract public function run(array $arguments);
    
    public function getUsage()
    {
        return $this->getName();
    }

    public function setFlags(array $flags)
    {
        $this->flags = $flags;
    }
}
