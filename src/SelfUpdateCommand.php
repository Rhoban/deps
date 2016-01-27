<?php

class SelfUpdateCommand extends Command
{
    public function getName()
    {
        return 'self-update';
    }

    public function getDescription()
    {
        return array('Updates deps itself');
    }

    public function run(array $arguments)
    {
        echo "* Updating deps from git...\n";
        $dir = $this->deps->getDirectory();
        OS::run("cd $dir; git pull");
    }
}
