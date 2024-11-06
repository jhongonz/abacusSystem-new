# Variables
PHP_BIN := php
COMPOSER := composer
PHPUNIT := vendor/bin/phpunit
PHPSTAN := vendor/bin/phpstan
PINT := vendor/bin/pint
INFECTION := vendor/bin/infection
export XDEBUG_MODE=coverage

install:
	$(COMPOSER) install

test:
	$(PHPUNIT)

test-coverage:
	$(PHPUNIT) --coverage-text

analyse:
	$(PHPSTAN) analyse app src tests --level=6

pint:
	$(PINT)

clean:
	rm -rf vendor composer.lock
	$(COMPOSER) install

infection:
	$(INFECTION)

all: install pint analyse test infection

tests: pint analyse test infection
