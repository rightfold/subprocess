all: type-check test

.PHONY: type-check
type-check:
	vendor/bin/psalm

.PHONY: test
test:
	vendor/bin/phpunit
