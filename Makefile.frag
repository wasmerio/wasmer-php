PHP_TEST_SETTINGS += -d report_memleaks=Off

.PHONY: examples
.SILENT: examples
examples: all
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

all: src/wasmer_root_arginfo.h src/wasmer_vec_arginfo.h  src/wasmer_exception_arginfo.h

documentation: .phpdoc/build/index.html

.PHONY: lint
lint: vendor/friendsofphp/php-cs-fixer/php-cs-fixer
	$< fix --dry-run --allow-risky=yes

.phpdoc/build/index.html: vendor/phpdocumentor/phpdocumentor/bin/phpdoc src/wasmer_*.stub.php
	$<

vendor/phpdocumentor/phpdocumentor/bin/phpdoc vendor/friendsofphp/php-cs-fixer/php-cs-fixer: composer.lock
	composer install

composer.lock: composer.json
	composer update

