<?php

class Deps
{
    protected $directory;
    protected $commands = array();
    protected $packages = array();

    public function __construct($directory)
    {
        $this->addCommand(new InstallCommand);
        $this->addCommand(new ListCommand);
        $this->addCommand(new PathesCommand('includes'));
        $this->addCommand(new PathesCommand('libraries'));
        $this->addCommand(new PathesCommand('binaries'));

        // Loading packages
        $this->directory = $directory;
        $dir = opendir($directory.'/packages');
        while ($file = readdir($dir)) {
            $fullName = $directory.'/packages/'.$file;
            if ($file[0] != '.' && is_dir($fullName)) {
                $package = new Package($fullName);
                $this->packages[$package->getName()] = $package;
            }
        }
    }

    public function hasPackage($name)
    {
        return isset($this->packages[$name]);
    }

    public function getPackage($name)
    {
        return $this->packages[$name];
    }

    public function getPackages()
    {
        return $this->packages;
    }

    protected function addCommand(Command $command)
    {
        $command->setDeps($this);
        $this->commands[$command->getName()] = $command;
    }

    public function run(array $args)
    {
        if (count($args)) {
            $command = array_shift($args);
            if (isset($this->commands[$command])) {
                try {
                    $this->commands[$command]->run($args);
                } catch (\Exception $error) {
                    echo "Error: ".$error->getMessage()."\n";
                }
                return;
            } else {
                echo "Error: Unknown command $command\n";
            }
        }

        $this->help();
    }

    public function help()
    {
        echo "deps v0.1, dependencies manager\n";
        echo "\n";
        foreach ($this->commands as $command) {
            echo $command->getName().": usage: deps ".$command->getUsage()."\n";
            echo '    '.implode("\n    ", $command->getDescription())."\n\n";
        }
    }

    public function clean($name)
    {
        return strtolower(str_replace('/', '_', $name));
    }

    public function getPackageDirectory($name)
    {
        return $this->directory . '/packages/' . $this->clean($name);
    }

    public function install($dep)
    {
        if (!$this->hasPackage($dep)) {
            echo "Installing $dep...\n";
            $target = $this->getPackageDirectory($dep);
            system("git clone --depth=1 https://github.com/$dep $target", $return);
            if ($return != 0) {
                system("rm -rf $target");
                throw new \Exception("Unable to install package $dep");
            } else {
                $package = new Package($target);
                $this->packages[$package->getName()] = $package;
            }
        } else {
            $this->update($dep);
        }
        $this->build($dep);
    }

    public function update($dep)
    {
        if ($this->hasPackage($dep)) {
            $package = $this->getPackage($dep);
            $package->update();
        } else {
            throw new \Exception("Unable to update not existing package $dep");
        }
    }

    public function build($dep)
    {
        if ($this->hasPackage($dep)) {
            $package = $this->getPackage($dep);
            $package->build();
        } else {
            throw new \Exception("Unable to build not existing package $dep");
        }
    }
}
