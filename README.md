# MODX CLI Tool


For best usage add a function to your .bashrc or .profile or the like
````
#!/bin/bash
function modx {
    /path/to/repo/modx.sh $@
}

## Usage #
````sh
# Create a config file in the current directory to point to a modx installation
$ modx init

# Search for packages on the extras repo
$ modx package search getresource

# Install a package from the repo
$ modx package install getResources

````
