#!/bin/bash

export VAGRANT_CWD=/home/vagrant/VM2/klanten/$1/$2/
export VAGRANT_HOME=/home/vagrant/.vagrant.d/
export HOME=/home/vagrant/
vagrant up testklant-test-web01
