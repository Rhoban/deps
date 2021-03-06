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
        $recursive = in_array('recursive', $this->flags);
		$unix = in_array('unix', $this->flags);
        $walked = array();
        $pathName = $this->name;
        $deps = $this->deps;

        $append = function ($name, $append) use ($recursive, $unix, &$walked, $pathName, $deps) {
            if (isset($walked[$name])) {
                return '';
            } else {
                $walked[$name] = true;
                $pathes = $deps->getPathes($pathName, array($name), $unix);
                if ($recursive && $deps->hasPackage($name)) {
                    $package = $deps->getPackage($name);
                    foreach ($package->getDependencies() as $dep) {
                        $pathes .= $append($dep, $append);
                    }
                }
                return $pathes;
            }
        };

        $pathes = '';
        if ($arguments) {
            foreach ($arguments as $package) {
                if ($recursive) {
                    $parts = explode(':', $package);
                    $package = $parts[0];
                }
                $pathes .= $append($package, $append);
            }
        } else {
            $pathes = $deps->getPathes($pathName, array(), $unix);
        }

        echo $pathes."\n";
    }
}
