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
DEPS_INCLUDES=`$DIR/deps includes`
DEPS_LIBRARIES=`$DIR/deps libraries`
DEPS_BINARIES=`$DIR/deps binaries`

export CPATH="$BASE_CPATH:$DEPS_INCLUDES"
export LIBRARY_PATH="$BASE_LIBRARY_PATH:$DEPS_LIBRARIES"
export LD_LIBRARY_PATH="$BASE_LD_LIBRARY_PATH:$DEPS_LIBRARIES"
export PATH="$BASE_PATH:$DEPS_BINARIES:$DIR"