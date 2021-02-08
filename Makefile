documentation: .phpdoc/build/index.html

.PHONY: lint
lint: vendor/friendsofphp/php-cs-fixer/php-cs-fixer
	$< fix --dry-run --allow-risky=yes

.phpdoc/build/index.html: vendor/phpdocumentor/phpdocumentor/bin/phpdoc ext/src/wasmer_*.stub.php
	$<

vendor/phpdocumentor/phpdocumentor/bin/phpdoc vendor/friendsofphp/php-cs-fixer/php-cs-fixer: composer.lock
	composer install

composer.lock: composer.json
	composer update