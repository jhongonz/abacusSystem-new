# Variables
PHP_BIN := php
COMPOSER := composer
PHPUNIT := ./vendor/bin/phpunit
PHPSTAN := ./vendor/bin/phpstan
PHP-CS-FIXER := ./vendor/bin/php-cs-fixer
INFECTION := ./vendor/bin/infection
export XDEBUG_MODE=coverage

c-install:
	$(COMPOSER) install

update:
	$(COMPOSER) update

test:
	$(PHPUNIT)

test-coverage:
	$(PHPUNIT) --coverage-text

test-deprecations:
	$(PHPUNIT) --display-phpunit-deprecations

test-debug:
	$(PHPUNIT) --debug

analyse:
	$(PHPSTAN) analyse

cs-fixer:
	$(PHP-CS-FIXER) fix

clean:
	rm -rf vendor composer.lock
	$(COMPOSER) install

infection:
	$(INFECTION)

install: c-install cs-fixer test-coverage

all: c-install cs-fixer analyse test infection

tests: cs-fixer analyse test infection
