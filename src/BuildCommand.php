<?php

class BuildCommand extends Command
{
    public function getName()
    {
        return 'build';
    }

    public function getUsage()
    {
        return 'build | build <packages>';
    }

    public function getDescription()
    {
        return array('If no argument given, build dependencies from deps.json.',
            'Else build given packages.');
    }

    public function run(array $arguments)
    {
        if ($arguments) {
            foreach ($arguments as $dep) {
                $this->deps->build($dep);
            }
        } else {
            $json = $this->deps->nearestJson();
            $package = new Package(dirname($json));
            $dependencies = $package->getDependencies();

            if ($dependencies) {
                foreach ($dependencies as $dep) {
                    $this->deps->build($dep);
                }
            } else {
                echo "Nothing to do!\n";
            }
        }

        return true;
    }
}
