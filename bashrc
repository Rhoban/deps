#!/bin/bash

# Saving default values for pathes
if [ "$DEPS_INITIALIZED" == "" ]; then
    export BASE_PATH=$PATH
    export BASE_CPATH=$CPATH
    export BASE_LIBRARY_PATH=$LIBRARY_PATH
    export BASE_LD_LIBRARY_PATH=$LD_LIBRARY_PATH
    export DEPS_INITIALIZED="1"
fi

# Current directory
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Getting deps includes, libraries and binaries
DEPS_INCLUDES=`php $DIR/deps.php includes`
DEPS_LIBRARIES=`php $DIR/deps.php libraries`
DEPS_BINARIES=`php $DIR/deps.php binaries:unix`

export CPATH="$DEPS_INCLUDES$BASE_CPATH"
export LIBRARY_PATH="$DEPS_LIBRARIES$BASE_LIBRARY_PATH"
export LD_LIBRARY_PATH="$DEPS_LIBRARIES$BASE_LD_LIBRARY_PATH"
export PATH="$DEPS_BINARIES$BASE_PATH"
export CMAKE_PREFIX_PATH="$CMAKE_PREFIX_PATH:$DIR"

function deps {
    php $DIR/deps.php $*
    if [ $? -eq 10 ]; then
        echo "Reloading variables..."
        source "$DIR/bashrc"
    fi
}
