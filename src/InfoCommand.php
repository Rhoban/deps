<?php

class InfoCommand extends Command
{
    public function getName()
    {
        return 'info';
    }

    public function getDescription()
    {
        return array('Print informations about current package');
    }

    public function run(array $arguments)
    {
        $json = $this->deps->nearestJson();
        $package = new Package(dirname($json));
        echo "From $json\n\n";
        echo "* project name: ".$package->getName()."\n";

        $dependencies = $package->getDependencies();
        if ($dependencies) {
            echo "* dependencies:\n";
            foreach ($dependencies as $dep) {
                echo "  - $dep ";
                if ($this->deps->hasPackage($dep)) {
                    echo "(installed)";
                } else {
                    echo "(not installed)";
                }
                echo "\n";
            }
        } else {
            echo "* no dependencies\n";
        }
    }
}
