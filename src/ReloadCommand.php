<?php

class ReloadCommand extends Command
{
    public function getName()
    {
        return 'reload';
    }

    public function getDescription()
    {
        return array('Reload the pathes');
    }

    public function run(array $arguments)
    {
        Terminal::info("Reloading pathes\n");
        return true;
    }
}
