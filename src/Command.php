<?php

abstract class Command
{
    protected $deps;

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
}
