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
        Terminal::info("From $json\n\n");
        Terminal::bold("* project name: ".$package->getName()."\n");

        $dependencies = $package->getDependencies();
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
