include ext/Makefile

.DEFAULT_GOAL := help

documentation: ## Generate documentation
documentation: .phpdoc/build/index.html

.PHONY: unit
test-unit: ## Run OO interface tests
test-unit: vendor/phpunit/phpunit/phpunit target/cache/phpunit
	$(PHP_EXECUTABLE) $(PHP_TEST_SETTINGS) -dextension=ext/modules/wasm.so $< --testdox --testsuite tests

.PHONY: lint
lint: ## Run CS lint on all PHP files
lint: vendor/friendsofphp/php-cs-fixer/php-cs-fixer target/cache/php-cs-fixer
	$(PHP_EXECUTABLE) $(PHP_TEST_SETTINGS) $< fix --dry-run --allow-risky=yes -v

.phpdoc/build/index.html: vendor/phpdocumentor/phpdocumentor/bin/phpdoc phpdoc.dist.xml target/cache/phpdocumentorr ext/src/wasmer_*.stub.php src/*.php src/Exception/*.php src/Type/*.php
	$<

vendor/phpdocumentor/phpdocumentor/bin/phpdoc vendor/friendsofphp/php-cs-fixer/php-cs-fixer vendor/phpunit/phpunit/phpunit: vendor/composer/installed.json

.SILENT: vendor/composer/installed.json
vendor/composer/installed.json: target/cache/composer composer.json
	composer install

.PHONY: test-examples
.SILENT: test-examples
test-examples: ## Run OO interface examples
test-examples: vendor/phpunit/phpunit/phpunit target/cache/phpunit
	$(PHP_EXECUTABLE) $(PHP_TEST_SETTINGS) -dextension=ext/modules/wasm.so $< --testdox --testsuite examples

.PHONY: test-all
.SILENT: test-all
test-all: ## Run all tests & examples (PHP module & OO interface)
	cd ext && NO_INTERACTION=1 make test
	cd ext && make examples
	make test-unit
	make test-examples

.PHONY: info
.SILENT: info
info: ## Display PHP module informations
info: PURPLE = $(shell tput setaf 5)
info: RESET = $(shell tput sgr0)
info: version
	@echo "${PURPLE}PHP Module${RESET}"
	$(PHP_EXECUTABLE) $(PHP_TEST_SETTINGS) -dextension=ext/modules/wasm.so --ri wasm | tail -n +4

.SILENT: version
version: ## Display PHP version
version: PURPLE = $(shell tput setaf 5)
version: RESET = $(shell tput sgr0)
version:
	@echo "${PURPLE}PHP Version${RESET}"
	$(PHP_EXECUTABLE) $(PHP_TEST_SETTINGS) -dextension=ext/modules/wasm.so -v

.SILENT: target
target:
	mkdir -p target

.SILENT: target/cache
target/cache: target
	mkdir -p target/cache

target/cache/%: target/cache
	@mkdir -p $@

.SILENT: help
help: ## Display this message
help: BLACK = $(shell tput setaf 0)
help: YELLOW = $(shell tput setaf 3)
help: BLUE = $(shell tput setaf 4)
help: PURPLE = $(shell tput setaf 5)
help: GREEN = $(shell tput setaf 72)
help: ORANGE = $(shell tput setaf 208)
help: LIGHTYELLOW = $(shell tput setaf 221)
help: GRAY = $(shell tput setaf 245)
help: WHITE = $(shell tput setaf 255)
help: RESET = $(shell tput sgr0)
help: UNDERLINE = $(shell tput smul)
help: NOUNDERLINE = $(shell tput rmul)
help:
	echo "${YELLOW}$@${RESET}"
	echo ""
	for MAKEFILE in $(MAKEFILE_LIST); do \
		DOC=$$(grep -E '^## .*$$' $$MAKEFILE); \
		TARGETS=$$(grep -E '^[a-zA-Z_0-9%-/ ]+:.*?## .*$$' $$MAKEFILE | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "${BLUE}    %-15s${RESET} %s\n", $$1, $$2}'); \
		VARIABLES=$$(grep -E '^\.?[a-zA-Z_0-9%-]+[ ]*[:\?]?=.*?## .*$$' $$MAKEFILE | sort | awk 'BEGIN {FS = "[:\?]?=.*?## "}; {printf "${BLUE}    %-15s${RESET} %s\n", $$1, $$2}'); \
		if [[ -n "$$TARGETS" || -n "$$VARIABLES" ]]; then \
			echo "${PURPLE}$$MAKEFILE${RESET}"; \
			if [ -n "$$DOC" ]; then \
			  echo "  $$DOC\n"; \
			fi; \
			if [ -n "$$TARGETS" ]; then \
				echo "  ${PURPLE}Targets${RESET}"; \
				echo "$$TARGETS\n"; \
		  	fi; \
			if [ -n "$$VARIABLES" ]; then \
			  	echo "  ${PURPLE}Variables${RESET}"; \
				echo "$$VARIABLES\n"; \
		  	fi; \
		fi; \
	done
	echo "${BLACK}----------------------------------------------------------------------------------------------------${RESET}"
	echo ""
	echo "Document targets and variables by adding ${GRAY}## help message${RESET} after its definition."
	echo ""
	echo "Example:"
	echo "  | ${WHITE}${UNDERLINE}foo${NOUNDERLINE}${RESET} := ${GREEN}\"bar\"${RESET} ${GRAY}## help for ${UNDERLINE}foo${NOUNDERLINE}${RESET}"
	echo "  |"
	echo "  | ${WHITE}${UNDERLINE}something${NOUNDERLINE}${RESET}: ${GRAY}## help for ${UNDERLINE}something${NOUNDERLINE}${RESET}"
	echo "  | ${WHITE}${UNDERLINE}something${NOUNDERLINE}${RESET}: ${LIGHTYELLOW}${UNDERLINE}prereq${NOUNDERLINE}${RESET}"
	echo "  | 	${ORANGE}echo${RESET} ${GREEN}\"recipe for ${UNDERLINE}something${NOUNDERLINE}\"${RESET}"

.SILENT: ext/Makefile
ext/Makefile: PHP_HOME ?=
ext/Makefile: ext/config.m4 ext/Makefile.frag
ifneq (,$(PHP_HOME))
	cd ext; $(PHP_HOME)/phpize && ./configure --with-php-config=$(PHP_HOME)/php-config
else
	cd ext; phpize && ./configure
endif

ext/configure: ext/Makefile

ext/test: export NO_INTERACTION = 1
ext/all ext/examples ext/test: ext/Makefile
	@cd ext; make $(subst ext/,,$@)