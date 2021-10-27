#!/bin/sh

# Config
# Create a temporary directory to prevent conflictions
tmpDirname=".tmp"
pluginName="gharar"
outputFilename="moodle-mod-gharar.zip"

# Exit on error
set -e

mainRepoDir="$(dirname "$(realpath "$0")")/.."
tmpDir="$mainRepoDir/../$tmpDirname"

# The resulting file path
zipFilePath="$mainRepoDir/$outputFilename"

# Remove dev dependencies from vendor/ directory. Otherwise, the resulting file
# will be huge in size. For example, 80KB versus 17MB for the directory alone.
# At last, we will revert this change back.
echo "Removing Composer dev dependencies..."
composer --no-dev install > /dev/null 2>&1

mkdir -p "$tmpDir"
cd "$tmpDir"

# Make sure the resulting file includes only one directory matching plugin's name
rsync -av "$mainRepoDir/" "./$pluginName" \
    --exclude "$outputFilename" --exclude ".git" --exclude ".gitignore" \
    --exclude ".phan" --exclude "bin/" --exclude ".php-cs-fixer.cache" \
    --exclude ".php-cs-fixer.dist.php" --exclude "psalm.xml" > /dev/null

# Remove previous zip file to prevent extra removed files to remain there
if [[ -e "$zipFilePath" ]]; then
    rm "$zipFilePath"
fi

# Create the zip file in the current directory
zip -r -y "$zipFilePath" "./$pluginName" > /dev/null

# Cleanup
cd "$mainRepoDir"
rm -r "$tmpDir"

echo "Zip file created successfully."

cd "$mainRepoDir"
echo "Re-adding Composer dev dependencies..."
composer install > /dev/null 2>&1
