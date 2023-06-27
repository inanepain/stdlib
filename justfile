# inanepain/stdlib
# version: $Id$
# date: $Date$

set shell := ["zsh", "-cu"]
set positional-arguments

project := "inane\\stdlib"

# list recipes
_default:
    @echo "{{project}}:"
    @just --list --list-heading ''

# generate php doc (v2) (all, cache, html)
php-doc clear="all":
	#!/usr/bin/env zsh
	if [ -d .phpdoc ] && [[ "{{clear}}" = "all" || "{{clear}}" = "cache" ]]; then
		echo "\tCleaning: cache..."
		rm -fr .phpdoc
	fi
	if [ -d doc/code ] && [[ "{{clear}}" = "all" || "{{clear}}" = "html" ]]; then
		echo "\tCleaning: html..."
		rm -fr doc/code
	fi

	mkdir -p doc/code
	phpdoc -d src -t doc/code --title="{{project}}" --defaultpackagename="Inane"
