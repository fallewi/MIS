
# This file should be used for variables that will be accessed across multiple tests
# All tests should source this file to include the vars

# Directory setup
TEST_DIR="$( cd ../"$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
REPO_ROOT=`git rev-parse --show-toplevel`

# Get the current branch
BRANCH=`git rev-parse --abbrev-ref HEAD`

# Confirm the branch passed in exists
#git show-ref --verify --quiet refs/heads/${BRANCH}
#if [ $? -ne 0 ]; then
#    echo "The branch \"${BRANCH}\" does not exist."
#    exit 1
#fi

# Only run tests against PHP files that exist in the Blue Acorn namespace
FILE_LIST=""
for i in $(git diff --name-only  $BRANCH..origin/develop | grep -e "app/code/local/BlueAcorn.*\.php$" | tr " " "\n"); do
    FILE_LIST=$FILE_LIST" /repo/"$i
done;

FILE_LIST=$(echo $FILE_LIST | tr -d '\n')