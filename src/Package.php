<?php

class Package
{
    protected $directory;
    protected $config = array();
    protected $name = '';

    public function __construct($directory)
    {
        $this->directory = $directory;
        $this->readConfig();
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function readConfig()
    {
        $jsonFile = $this->directory.'/deps.json';
        if (file_exists($jsonFile)) {
            if (!($this->config = json_decode(file_get_contents($jsonFile), true))) {
                $this->config = array();
            }
        }
        $this->name = strtolower(isset($this->config['name']) ? $this->config['name'] 
            : basename($this->directory));
    }

    public function getPathes($name)
    {
        if (isset($this->config[$name])) {
            if (is_array($this->config[$name])) {
                $pathes = $this->config[$name];
            } else {
                $pathes = array($this->config[$name]);
            }
        } else {
            $pathes = array();
        }

        foreach ($pathes as &$path) {
			$path = $this->directory.'/'.$path;
			if ($name == 'binaries') {
				$path = OS::bashize($path);
			}
        }

        return $pathes;
    }

    public function getIncludes()
    {
        return $this->getPathes('includes');
    }
    
    public function getLibraries()
    {
        return $this->getPathes('libraries');
    }
    
    public function getBinaries()
    {
        return $this->getPathes('binaries');
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDependencies()
    {
        if (isset($this->config['deps'])) {
            return $this->config['deps'];
        } else {
            return array();
        }
    }

    public function build()
    {
        if (isset($this->config['build'])) {
			$build = $this->config['build'];
			OS::tweakBuild($build);
            OS::run('cd '.OS::bashize($this->directory).';'.implode(';', $build));
        }
    }

    public function update()
    {
        if (isset($this->config['build'])) {
            OS::run('cd '.OS::bashize($this->directory).';git pull');
            $this->readConfig();
        }
    }
}
