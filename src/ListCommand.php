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
        echo "Installed packages:\n";
        foreach ($this->deps->getPackages() as $package) {
            echo '* '.$package->getName()."\n";
        }
    }
}
