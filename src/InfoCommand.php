<?php

class InfoCommand extends Command
{
    public function getName()
    {
        return 'info';
    }

    public function getDescription()
    {
        return array('Print informations about current package',
        'Passing "porcelain" as argument will output machine-readable list');
    }

    public function getUsage()
    {
        return 'infos [porcelain]';
    }

    public function run(array $arguments)
    {
        $json = $this->deps->nearestJson();
        $package = new Package(dirname($json));
        $porcelain = (count($arguments) && $arguments[0] == 'porcelain');
        if (!$porcelain) {
            Terminal::info("From $json\n\n");
            Terminal::bold("* project name: ".$package->getName()."\n");
        }

        $dependencies = $package->getDependencies();
        if ($porcelain) {
            echo implode(':', $dependencies)."\n";
        } else {
            if ($dependencies) {
                Terminal::bold("* dependencies:\n");
                foreach ($dependencies as $dep) {
                    echo "  - $dep ";
                    if ($this->deps->hasPackage($dep)) {
                        Terminal::success("(installed)");
                    } else {
                        Terminal::warning("(not installed)");
                    }
                    echo "\n";
                }
            } else {
                Terminal::bold("* no dependencies\n");
            }
        }
    }
}
