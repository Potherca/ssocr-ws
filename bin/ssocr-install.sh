#!/usr/bin/env bash

# For all options see http://www.tldp.org/LDP/abs/html/options.html
set -o nounset      # Exit script on use of an undefined variable, same as "set -u"
set -o errexit      # Exit script when a command exits with non-zero status, same as "set -e"
set -o pipefail     # Makes pipeline return the exit status of the last command in the pipe that failed

# ==============================================================================
#                            APPLICATION VARS
# ------------------------------------------------------------------------------
readonly sInstallDirectory='ssocr-install'
readonly sTarbalPath='ssocr.tar.bz2'
readonly sTarbalUrl='https://www.unix-ag.uni-kl.de/~auerswal/ssocr/ssocr-2.16.3.tar.bz2'

declare g_bCleanup=true
declare -i g_iExitCode=0
# ==============================================================================


# ==============================================================================
function printStatus() {
# ------------------------------------------------------------------------------
    echo " -----> $*"
}
# ==============================================================================

# ==============================================================================
function printTopic() {
# ------------------------------------------------------------------------------
    echo " =====> $*"
}
# ==============================================================================


# ==============================================================================
function printError() {
# ------------------------------------------------------------------------------
  echo
  echo " !     ERROR: $*" 1>&2
  echo
}
# ==============================================================================

# ==============================================================================
function cleanup() {
# ------------------------------------------------------------------------------
    if [ "${g_bCleanup}" == "true" ];then
        printTopic 'Cleaning up...'
        rm -rdf "${sInstallDirectory}" "${sTarbalPath}"
    fi
}
# ==============================================================================

# ==============================================================================
function ssocr-install() {
# ------------------------------------------------------------------------------
    printTopic 'SSOCR Installer'

    trap cleanup EXIT

    iResult=`command -v ssocr > /dev/null && echo 0 || echo 1`

    if [ "${iResult}" == "0" ];then
        printStatus 'ssocr is already installed'
        g_bCleanup=false
    elif [ "${iResult}" == "1" ];then
        printStatus 'ssocr is not installed'

        printStatus "Downloading ssocr tarball from ${sTarbalUrl}"
        wget --output-document="${sTarbalPath}" --progress=bar "${sTarbalUrl}"

        printStatus 'Unpacking tarball'
        mkdir -p "${sInstallDirectory}"
        tar --bzip2 --extract --file="${sTarbalPath}" --directory="${sInstallDirectory}" --strip-components 1
        pushd "${sInstallDirectory}"

        printStatus 'Installing dependencies'
        sudo apt-get install --yes libimlib2-dev

        printStatus 'Building binary'
        make

        printStatus 'Installing binary'
        sudo make install
        popd

        printStatus 'Copying binary for safe keeping'
        cp "${sInstallDirectory}/ssocr" "$(dirname $0)"
    else
        echo $iResult
        g_iExitCode=66
        printError 'An unknown error occurred'
    fi

    exit ${g_iExitCode}
}
# ==============================================================================


if [[ ${BASH_SOURCE[0]} != $0 ]]; then
  export -f ssocr-install
else
  ssocr-install "${@}"
  exit $?
fi

#EOF
