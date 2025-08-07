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
	if [ -d documentation/code ] && [[ "{{clear}}" = "all" || "{{clear}}" = "html" ]]; then
		echo "\tCleaning: html..."
		rm -fr documentation/code
	fi

	mkdir -p documentation/code
	phpdoc -d src -t documentation/code --title="{{project}}" --defaultpackagename="Inane"
