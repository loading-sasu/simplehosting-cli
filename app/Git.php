<?php

namespace App;

class Git
{
    public $conf;

    private $remotes = [];

    public function __construct()
    {
        $remotes = collect(explode("\n", trim(`git remote show`)));
        $remotes->each(function ($name) {
            $this->remotes[$name] = parse_url(trim(`git remote get-url $name`));
            $this->remotes[$name]['remote'] = $name;
            $this->remotes[$name]['sftp'] = preg_replace('|git\.|iU', "sftp.", $this->remotes[$name]['host']);
            $this->remotes[$name]['repository'] = preg_replace('|^/(\S+(\.git)?)$|iU', "$1", $this->remotes[$name]['path']);
            $this->remotes[$name]['website'] = preg_replace('|^/(\S+)\.git$|iU', "$1", $this->remotes[$name]['path']);
        });
        $this->remotes = collect($this->remotes);
    }

    public function getRemotes()
    {
        return $this->remotes;
    }

    public function getRemote($name)
    {
        if ($this->getRemotes()->has($name)) {
            return $this->getRemotes()->get($name);
        }
    }
}
