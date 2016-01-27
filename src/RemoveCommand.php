<?php

class RemoveCommand extends Command
{
    public function getName()
    {
        return 'remove';
    }

    public function getUsage()
    {
        return 'remove <packages>';
    }

    public function getDescription()
    {
        return array('Remove given packages');
    }

    public function run(array $arguments)
    {
        foreach ($arguments as $dep) {
            echo "* Removing $dep...\n";
            $dir = $this->deps->getPackageDirectory($dep);
            `rm -rf $dir`;
        }
    }
}
