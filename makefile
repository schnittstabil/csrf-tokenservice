.PHONY: doc lint phpunit test

test: lint phpunit

clean:
	@tput setaf 2; echo cleaning up...; tput sgr0
	@rm -rf build
	@rm -rf coverage
	@rm -rf doc

doc:
	@vendor/bin/sami.php update sami_configuration.php -v

lint:
	@tput setaf 2; echo running lint...; tput sgr0
	@vendor/bin/phpcs -s
	@vendor/bin/php-cs-fixer fix --dry-run --diff Schnittstabil
	@vendor/bin/php-cs-fixer fix --dry-run --diff tests
	@vendor/bin/php-cs-fixer fix --dry-run --diff *.php

phpunit:
	@tput setaf 2; echo running phpunit...; tput sgr0
	@vendor/bin/phpunit

watch:
	@gazer-color --pattern "Schnittstabil/**/*" -- make lint phpunit doc
