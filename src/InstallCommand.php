<?php

class InstallCommand extends Command
{
    public function getName()
    {
        return 'install';
    }

    public function getUsage()
    {
        return 'install | install <packages>';
    }

    public function getDescription()
    {
        return array('If no argument given, install dependencies from deps.json.',
            'Else install given packages.');
    }

    public function run(array $arguments)
    {
        if ($arguments) {
            foreach ($arguments as $dep) {
                $this->deps->install($dep);
            }
        } else {
            $package = new Package(getcwd());
            $dependencies = $package->getDependencies();

            if ($dependencies) {
                foreach ($dependencies as $dep) {
                    $this->deps->install($dep);
                }
            } else {
                echo "Nothing to do!\n";
            }
        }

        return true;
    }
}
