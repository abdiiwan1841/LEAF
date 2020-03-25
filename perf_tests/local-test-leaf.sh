#!/bin/bash

export PROJECTS_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )"/. && pwd )"
echo "Using PROJECTS_DIR = $PROJECTS_DIR"

cat "${PROJECTS_DIR}/properties/local.properties"

~/Downloads/apache-jmeter-5.2.1/bin/jmeter -n -t "${PROJECTS_DIR}/leaf.jmx" -p "${PROJECTS_DIR}/properties/local.properties"



