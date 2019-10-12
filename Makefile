help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  unit-test                Run unit tests"
	@echo "  coverage                 Show code coverage"


unit-test:
	@composer install \
	&& ./vendor/bin/phpunit \

coverage: unit-test
	php coverage-checker.php ./data/clover.xml 100

