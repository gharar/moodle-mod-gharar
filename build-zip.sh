#!/bin/sh

# Config
# Create a temporary directory to prevent conflictions
tmpDirname='.tmp'
pluginName='gharar'
outputFilename='moodle-mod-gharar.zip'

mainRepoDir="$(pwd)"
tmpDir="$mainRepoDir/../$tmpDirname"

mkdir "$tmpDir"
cd "$tmpDir"

# Make sure the resulting file includes only one directory matching the plugin's name
rsync -av "$mainRepoDir/" "./$pluginName" --exclude ".git" > /dev/null

# Create the zip file in the current directory
zip -r "$mainRepoDir/$outputFilename" "./$pluginName" > /dev/null

# Cleanup
cd "$mainRepoDir"
rm -r "$tmpDir"

echo "Zip file created successfully."
