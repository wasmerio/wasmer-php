include ext/Makefile

documentation: .phpdoc/build/index.html

.PHONY: unit
unit: vendor/atoum/atoum/bin/atoum
	PHP_EXECUTABLE=$(PHP_EXECUTABLE) $<

.PHONY: lint
lint: vendor/friendsofphp/php-cs-fixer/php-cs-fixer
	$(PHP_EXECUTABLE) $< fix --dry-run --allow-risky=yes

.phpdoc/build/index.html: vendor/phpdocumentor/phpdocumentor/bin/phpdoc phpdoc.dist.xml ext/src/wasmer_*.stub.php src/*.php src/Exception/*.php src/Type/*.php
	$<

vendor/phpdocumentor/phpdocumentor/bin/phpdoc vendor/friendsofphp/php-cs-fixer/php-cs-fixer vendor/atoum/atoum/bin/atoum: composer.lock
	composer install

composer.lock: composer.json
	composer update