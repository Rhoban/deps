<?php

class RemoteCommand extends Command
{
    public function getName()
    {
        return 'remote';
    }

    public function getDescription()
    {
        return array('Print remotes information');
    }

    public function getUsage()
    {
        return 'remote';
    }

    public function run(array $arguments)
    {
        $remotes = $this->deps->getRemotes();
        $current = $remotes->getCurrent();
        Terminal::info("Remotes:\n");
        foreach ($remotes->getRemotes() as $remote => $addr) {
            if ($remote == $current) {
                Terminal::success("* $remote ($addr)\n");
            } else {
                Terminal::bold("* $remote ($addr)\n");
            }
        }
    }
}
