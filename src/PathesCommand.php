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
        $walked = array();
        $pathName = $this->name;
        $deps = $this->deps;

        $append = function ($name, $append) use ($recursive, &$walked, $pathName, $deps) {
            if (isset($walked[$name])) {
                return '';
            } else {
                $walked[$name] = true;
                $pathes = $deps->getPathes($pathName, array($name));
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
                $pathes .= $append($package, $append);
            }
        } else {
            $pathes = $deps->getPathes($pathName);
        }

        echo $pathes."\n";
    }
}
