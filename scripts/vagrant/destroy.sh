#!/bin/bash

if [[ ! -z $1 ]]; then
	vagrant destroy $1 --force
else
	vagrant destroy --force
fi
