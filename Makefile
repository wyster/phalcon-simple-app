env-file ?= ./.env

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  setup                    Setup default setting for simple run"
	@echo "  unit-test                Run unit tests"
	@echo "  coverage                 Show code coverage"

setup:
	@bash -c "cp -n ./.env.example ./.env"

unit-test:
	[ ! -f $(env-file) ] && echo "Env file not found" && exit 1 || \
	docker run --env-file $(env-file) -it --rm -v `pwd`:`pwd` -w `pwd` phalcon-simple-app /bin/bash -c " \
		docker-php-ext-enable xdebug && \
		rm -f ./data/clover.xml && \
		./vendor/bin/phpunit" \

coverage:
	[ ! -f ./data/clover.xml ] && echo "Need run make unit-test before" && exit 1 || \
	docker run -it --rm -v `pwd`:`pwd` -w `pwd` phalcon-simple-app \
		php coverage-checker.php ./data/clover.xml 100

