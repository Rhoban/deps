<?php

class Remotes
{
    protected $remotes;
    protected $current;

    public function __construct()
    {
        $data = json_decode(file_get_contents(__DIR__.'/../remotes.json'), true);
        $this->remotes = $data['remotes'];
        $this->current = $data['current'];
    }

    public function getRemotes()
    {
        return $this->remotes;
    }

    public function getCurrent()
    {
        return $this->current;
    }
}
