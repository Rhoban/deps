<?php

class CFlagsCommand extends Command
{
    public function getName()
    {
        return 'cflags';
    }

    public function getUsage()
    {
        return 'cflags <packages>';
    }

    public function getDescription()
    {
        return array('Returns the CFlags for given libraries');
    }

    public function cflags(Package $package)
    {
        $flags = '';
        foreach ($package->getPathes('includes') as $include) {
            $flags .= '-I'.$include.' ';
        }
        foreach ($package->getPathes('links') as $link) {
            $flags .= ''.$link.' ';
            $flags .= '-Wl,-rpath,'.dirname($link).' ';
        }
        return $flags;
    }

    public function run(array $arguments)
    {
        $flags = "";
        if ($arguments) {
            foreach ($arguments as $dep) {
                if ($this->deps->hasPackage($dep)) {
                    $flags .= $this->cflags($this->deps->getPackage($dep));
                }
            }
        } else {
            $json = $this->deps->nearestJson();
            $package = new Package(dirname($json));
            $dependencies = $package->getDependencies();

            foreach ($dependencies as $dep) {
                if ($this->deps->hasPackage($dep)) {
                    $flags .= $this->cflags($this->deps->getPackage($dep));
                }
            }
        }

        Terminal::write($flags."\n");
    }
}
