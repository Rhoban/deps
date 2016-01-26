<?php

class UpgradeCommand extends Command
{
    public function getName()
    {
        return 'upgrade';
    }

    public function getUsage()
    {
        return 'upgrade | upgrade <packages>';
    }

    public function getDescription()
    {
        return array('If no argument given, upgrade all packages. Else upgrade given ones');
    }

    public function run(array $arguments)
    {
        if ($arguments) {
            foreach ($arguments as $dep) {
                $this->deps->update($dep);
                $this->deps->build($dep);
            }
        } else {
            foreach ($this->deps->getPackages() as $package) {
                $dep = $package->getName();
                $this->deps->update($dep);
                $this->deps->build($dep);
            }
        }

        return true;
    }
}
