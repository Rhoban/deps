# deps, lightweight packages manager

## Installation

First, clone this repository:

    git clone https://github.com/Rhoban/deps.git

Then, add this line to your `.bashrc`:

    source "$HOME/deps/bashrc"

## Usage

You can install packages with command line like:

    deps install rhobandeps/jsoncpp

This will clone, compile and add the dependency to your environment variables
(namely `PATH`, `CPATH`, `LIBRARY_PATH`, `LD_LIBRARY_PATH`).

Installed packages can be listed with:

    deps list

You can then remove them:

    deps remove rhobandeps/jsoncpp

## `deps.json` file

You can specify dependencies for your project using `deps.json` file, here is
an example:

```json
{
    "name": "me/myproject",
    "build": ["make"],
    "deps": [
        "rhobandeps/jsoncpp"
    ]
}
```

If you have such a `deps.json` file, you can simply run `deps install` from your project
tree, this will install all the dependencies.
