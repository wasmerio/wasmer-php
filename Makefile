include ext/Makefile

documentation: .phpdoc/build/index.html

.PHONY: unit
unit: vendor/atoum/atoum/bin/atoum
	PHP_EXECUTABLE="$(PHP_EXECUTABLE)" $(PHP_EXECUTABLE) $(PHP_TEST_SETTINGS) -dzend_extension=xdebug.so $<

.PHONY: lint
lint: vendor/friendsofphp/php-cs-fixer/php-cs-fixer
	$< fix --dry-run --allow-risky=yes -v

.phpdoc/build/index.html: vendor/phpdocumentor/phpdocumentor/bin/phpdoc phpdoc.dist.xml ext/src/wasmer_*.stub.php src/*.php src/Exception/*.php src/Type/*.php
	$<

vendor/phpdocumentor/phpdocumentor/bin/phpdoc vendor/friendsofphp/php-cs-fixer/php-cs-fixer vendor/atoum/atoum/bin/atoum: composer.lock
	composer install

composer.lock: composer.json
	composer update

.PHONY: oo-examples
.SILENT: oo-examples
oo-examples:
	FAILURES=(); \
	for EXAMPLE in examples/*.php; \
	do \
		echo "====================================================================="; \
		echo "> Running $$EXAMPLE"; \
		echo "---------------------------------------------------------------------"; \
		if ! $(PHP_EXECUTABLE) $(PHP_TEST_SETTINGS) -d extension_dir=$(top_builddir)/modules/ $(PHP_TEST_SHARED_EXTENSIONS) $$EXAMPLE; \
		then \
			FAILURES+=($$EXAMPLE); \
		fi; \
		echo; \
	done; \
	if [ $${#FAILURES[@]} -gt 0 ]; \
	then \
		echo "====================================================================="; \
		echo "> Failed examples summary"; \
		echo "---------------------------------------------------------------------"; \
		for FAILURE in $${FAILURES[@]}; \
		do \
			echo "* $$FAILURE"; \
		done; \
		echo; \
		exit $${#FAILURES[@]}; \
	fi;
