#!/bin/bash

set -e

if [ $# -ne 2 ]; then
  echo "Usage: $0 <repo> <folder>"
  echo "Example: $0 ui UI"
  exit 1
fi

REPO_NAME=$1
FOLDER_NAME=$2

REMOTE_REPO="git@github.com:moonshine-software/${REPO_NAME}.git"
REMOTE_BRANCH="4.x"
LOCAL_PREFIX="src/${FOLDER_NAME}"
TMP_BRANCH="repair-split-${REPO_NAME}"

echo "Splitting $LOCAL_PREFIX into $REMOTE_REPO branch $REMOTE_BRANCH..."

git subtree split --prefix="$LOCAL_PREFIX" -b "$TMP_BRANCH"
git push --force "$REMOTE_REPO" "$TMP_BRANCH:$REMOTE_BRANCH"
git branch -D "$TMP_BRANCH"

echo "Repair complete for $REPO_NAME → $REMOTE_BRANCH"

