<?php

class LinkCommand extends Command
{
    public function getName()
    {
        return 'link';
    }

    public function getDescription()
    {
        return array('Links the current project as package');
    }

    public function run(array $arguments)
    {
        $json = $this->deps->nearestJson();
        $linkDir = dirname($json);
        $package = new Package(dirname($json));
        $name = $package->getName();

        if (!$name) {
            echo "Error: no name for package\n";
        } else {
            echo "Do you want to create symlink from $name to $linkDir? (yes/no)\n";
            $l = readline();
            if (trim($l) == 'yes') {
                echo "* Linking package $name to $linkDir...\n";
                $dir = $this->deps->getPackageDirectory($package->getName());
                if (is_dir($dir)) {
                    OS::run("rm -rf $dir");
                }
                OS::run("ln -s $linkDir $dir");
            } else {
                echo "Aborting.\n";
            }
        }
    }
}
