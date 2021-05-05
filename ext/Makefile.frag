## Targets from this Makefile are available from the top-level Makefile using the `ext/` prefix.

.PHONY: examples
.SILENT: examples
examples: ## Run PHP module examples
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

all: ## Build PHP module
all: src/wasmer_root_arginfo.h src/wasmer_vec_arginfo.h src/wasmer_exception_arginfo.h src/wasmer_class_arginfo.h

test: ## Run PHP module tests

configure: ## Configure PHP module build system (use PHP_HOME to change the PHP binaries to use)
