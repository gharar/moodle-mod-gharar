#!/bin/sh

# Config
# Create a temporary directory to prevent conflictions
tmpDirname=".tmp"
pluginName="gharar"
outputFilename="moodle-mod-gharar.zip"

mainRepoDir="$(pwd)"
tmpDir="$mainRepoDir/../$tmpDirname"

# Remove dev dependencies from vendor/ directory. Otherwise, the resulting file
# will be huge in size. For example, 80KB versus 17MB for the directory alone.
# At last, we will revert this change back.
composer --no-dev install > /dev/null

mkdir "$tmpDir"
cd "$tmpDir"

# Make sure the resulting file includes only one directory matching plugin's name
rsync -av "$mainRepoDir/" "./$pluginName" \
    --exclude ".git" --exclude ".gitignore" --exclude "build-zip.sh" > /dev/null

# Remove previous zip file to prevent files to remain there
# TODO: Remove this?
rm "$mainRepoDir/$outputFilename"
# Create the zip file in the current directory
zip -r "$mainRepoDir/$outputFilename" "./$pluginName" > /dev/null

# Cleanup
cd "$mainRepoDir"
rm -r "$tmpDir"

# Revert Composer dev dependencies back
cd "$mainRepoDir"
composer install > /dev/null

echo "Zip file created successfully."
