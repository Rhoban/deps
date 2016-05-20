<?php

class Deps
{
    protected $directory;
    protected $remotes;
    protected $commands = array();
    protected $packages = array();
    protected $installed = array();

    public function __construct($directory)
    {
        $this->remotes = new Remotes;

        $this->addCommand(new InstallCommand);
        $this->addCommand(new LinkCommand);
        $this->addCommand(new UpgradeCommand);
        $this->addCommand(new BuildCommand);
        $this->addCommand(new RemoveCommand);
        $this->addCommand(new InfoCommand);
        $this->addCommand(new ListCommand);
        $this->addCommand(new StatusCommand);
        $this->addCommand(new SelfUpdateCommand);
        $this->addCommand(new ReloadCommand);
        $this->addCommand(new PathesCommand('includes'));
        $this->addCommand(new PathesCommand('libraries'));
        $this->addCommand(new PathesCommand('binaries'));
        $this->addCommand(new PathesCommand('links'));

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

    public function getRemotes()
    {
        return $this->remotes;
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
                    Terminal::error("Error: ".$error->getMessage()."\n");
                }
                return;
            } else {
                Terminal::error("Error: Unknown command $command\n");
            }
        }

        $this->help();
    }

    public function help()
    {
        Terminal::info("deps v0.1, dependencies manager\n");
        Terminal::info("\n");
        foreach ($this->commands as $command) {
            Terminal::bold($command->getName());
            Terminal::info(": usage: deps ".$command->getUsage()."\n");
            Terminal::write('    '.implode("\n    ", $command->getDescription())."\n\n");
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

    public function getPathes($name, array $packages=array())
    {
        $separator = ($name == 'binaries' ? ':' : PATH_SEPARATOR);
        $pathes = array();
        foreach ($this->getPackages() as $package) {
            if (!$packages || in_array($package->getName(), $packages)) {
                $pathes = array_merge($pathes, $package->getPathes($name));
            }
        }

        $pathes = implode($separator, $pathes);
        if ($pathes) {
            $pathes .= $separator;
        }
        return $pathes;
    }

    protected function updateEnv()
    {
        $binaries = $this->getPathes('binaries');
        $libraries = $this->getPathes('libraries');
        $includes = $this->getPathes('includes');

        $base=getenv('BASE_PATH');
        putenv("PATH=$binaries$base");
        $base=getenv('BASE_CPATH');
        putenv("CPATH=$includes$base");
        $base=getenv('BASE_LIBRARY_PATH');
        putenv("LIBRARY_PATH=$libraries$base");
        $base=getenv('BASE_LD_LIBRARY_PATH');
        putenv("LD_LIBRARY_PATH=$libraries$base");
    }

    public function install($dep, $rebuildDeps = true)
    {
        if (isset($this->installed[$dep])) {
            return;
        }
        $this->installed[$dep] = true;

        if (!$this->hasPackage($dep)) {
            Terminal::info("* Installing $dep...\n");
            $target = $this->getPackageDirectory($dep);
            if (is_dir($target)) {
                OS::run("rm -rf $target");
            }
            $btarget = OS::bashize($target);
            $remotes = $this->remotes->getRemotes();
            $addr = $remotes[$this->remotes->getCurrent()];
            $addr = sprintf($addr, $dep);
            $return = OS::run("git clone --depth=1 $addr $btarget");
            if ($return != 0) {
                OS::run("rm -rf $target");
                throw new \Exception("Unable to install package $dep");
            }
            $package = new Package($target);
            if (!$package->hasConfig()) {
                OS::run("rm -rf $btarget");
                throw new \Exception("$dep doesn't look like a deps package (no deps.json)");
            }
            $package->updateRemotes($this->remotes, true);
            $this->packages[$package->getName()] = $package;
        } else {
            $this->update($dep);
            $package = $this->getPackage($dep);
        }
        $package->readConfig();
        foreach ($package->getDependencies() as $sdep) {
            if ($rebuildDeps) {
                $this->install($sdep);
            } else {
                if (!$this->hasPackage($sdep)) {
                    $this->install($sdep);
                }
            }
        }
        $this->build($dep);
    }

    public function update($dep)
    {
        Terminal::info("* Updating $dep...\n");
        if ($this->hasPackage($dep)) {
            $package = $this->getPackage($dep);
            $package->update($this->remotes);
        } else {
            throw new \Exception("Unable to update not existing package $dep");
        }
    }

    public function build($dep)
    {
        $this->updateEnv();
        Terminal::info("* Building $dep...\n");
        if ($this->hasPackage($dep)) {
            $package = $this->getPackage($dep);
            $package->build();
        } else {
            throw new \Exception("Unable to build not existing package $dep");
        }
    }
}
