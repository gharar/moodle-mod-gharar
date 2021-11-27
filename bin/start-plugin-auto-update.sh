#!/bin/sh

# A script for automatically update minified built JS files using Grunt, update
# plugin files regularly, and get built JS file updates from there to the
# current repository.

if [ "$#" -lt 1 ]; then
    echo "Too fee arguments."
    exit 1
fi

# Kill all background processes on exit
trap "exit" INT TERM
trap "kill 0" EXIT

pluginPath="$1"

while inotifywait -r -e modify,create,delete,move .; do
    rsync -a --delete --progress . "$pluginPath/" --exclude .git \
        --exclude amd/build --exclude moodle-mod-gharar.zip
done &

while inotifywait -r -e modify,create,delete,move "$pluginPath/amd/build/"; do
    rsync -a --delete --progress "$pluginPath/amd/build/" ./amd/build/
done &

cd "$pluginPath/amd"
grunt watch
