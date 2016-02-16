<?php

class ListCommand extends Command
{
    public function getName()
    {
        return 'list';
    }

    public function getDescription()
    {
        return array('List installed packages');
    }

    public function run(array $arguments)
    {
        Terminal::info("Installed packages:\n");
        foreach ($this->deps->getPackages() as $package) {
            Terminal::bold('* '.$package->getName()."\n");
        }
    }
}
