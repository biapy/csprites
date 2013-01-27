#!/bin/bash
# Detect a file MIME type.

SCRIPT_DIR="$(command dirname "$0")"
GVFS_INFO="$(command which 'gvfs-info')"

# Read command line arguments or print usage strings if problem occurs.
TESTED_FILE="$1"

if [ -f "${TESTED_FILE}" ]; then
  if [ -x "${GVFS_INFO}" ]; then
    ${GVFS_INFO} --attributes="standard::content-type" "${TESTED_FILE}" \
                | command grep "standard::content-type" | command cut -c27-
  else
    command file --brief --mime-type "${TESTED_FILE}"
  fi
else
  echo "Usage :
get-mime-type.sh tested_file"
  exit 1
fi

exit 0
