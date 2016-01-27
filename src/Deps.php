<?php

class Deps
{
    protected $directory;
    protected $commands = array();
    protected $packages = array();

    public function __construct($directory)
    {
        $this->addCommand(new InstallCommand);
        $this->addCommand(new LinkCommand);
        $this->addCommand(new UpgradeCommand);
        $this->addCommand(new BuildCommand);
        $this->addCommand(new RemoveCommand);
        $this->addCommand(new InfoCommand);
        $this->addCommand(new ListCommand);
        $this->addCommand(new SelfUpdateCommand);
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

    public function getDirectory()
    {
        return $this->directory;
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

    public function nearestJson()
    {
        $directory = getcwd();

        while (!file_exists($directory.'/deps.json')) {
            $newDir = dirname($directory);
            if ($newDir == $directory) {
                throw new \Exception('No deps.json found (you are not in a deps project)');
            } else {
                $directory = $newDir;
            }
        }

        return $directory.'/deps.json';
    }

    public function run(array $args)
    {
        if (count($args)) {
            $command = array_shift($args);
            if (isset($this->commands[$command])) {
                try {
                    if ($this->commands[$command]->run($args)) {
                        exit(10);
                    }
                } catch (\Exception $error) {
                    echo "Error: ".$error->getMessage()."\n";
                }
                $bashrc = $this->directory.'/bashrc';
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
        return trim(strtolower(str_replace('/', '_', $name)));
    }

    public function getPackageDirectory($name)
    {
        return trim($this->directory . '/packages/' . $this->clean($name));
    }

    public function getPathes($name)
    {
        $pathes = array();
        foreach ($this->getPackages() as $package) {
            $pathes = array_merge($pathes, $package->getPathes($name));
        }

        return implode(':', $pathes);
    }

    protected function updateEnv()
    {
        $binaries = $this->getPathes('binaries');
        $libraries = $this->getPathes('libraries');
        $includes = $this->getPathes('includes');

        $base=getenv('BASE_PATH');
        putenv("PATH=$binaries:$base");
        $base=getenv('BASE_CPATH');
        putenv("CPATH=$includes:$base");
        $base=getenv('BASE_LIBRARY_PATH');
        putenv("LIBRARY_PATH=$libraries:$base");
        $base=getenv('BASE_LD_LIBRARY_PATH');
        putenv("LD_LIBRARY_PATH=$libraries:$base");

    }

    public function install($dep)
    {
        if (!$this->hasPackage($dep)) {
            echo "* Installing $dep...\n";
            $target = $this->getPackageDirectory($dep);
            if (is_dir($target)) {
                `rm -rf $target`;
            }
            system("git clone --depth=1 https://github.com/$dep $target", $return);
            if ($return != 0) {
                system("rm -rf $target");
                throw new \Exception("Unable to install package $dep");
            }
            $package = new Package($target);
            $this->packages[$package->getName()] = $package;
        } else {
            $this->update($dep);
            $package = $this->getPackage($dep);
        }
        $package->readConfig();
        foreach ($package->getDependencies() as $sdep) {
            $this->install($sdep);
        }
        $this->build($dep);
    }

    public function update($dep)
    {
        echo "* Updating $dep...\n";
        if ($this->hasPackage($dep)) {
            $package = $this->getPackage($dep);
            $package->update();
        } else {
            throw new \Exception("Unable to update not existing package $dep");
        }
    }

    public function build($dep)
    {
        $this->updateEnv();
        echo "* Building $dep...\n";
        if ($this->hasPackage($dep)) {
            $package = $this->getPackage($dep);
            $package->build();
        } else {
            throw new \Exception("Unable to build not existing package $dep");
        }
    }
}
