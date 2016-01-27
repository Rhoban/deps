<?php

class PathesCommand extends Command
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return array('Display the '.$this->name.' pathes');
    }

    public function run(array $arguments)
    {
        echo $this->deps->getPathes($this->name)."\n";
    }
}
