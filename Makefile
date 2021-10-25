#!/usr/bin/make

THIS_FILE := $(lastword $(MAKEFILE_LIST))

.PHONY : help build up up-all start down destroy stop restart logs ps shell env install install-dev migrate

.DEFAULT_GOAL := help

help:
	make -pRrq  -f $(THIS_FILE) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$'
build:
	docker-compose build $(container)
up-all:
	docker-compose up -d
up:
	docker-compose up -d $(container)
start:
	docker-compose start $(container)
down:
	docker-compose down $(container)
destroy:
	docker-compose down -v $(container)
stop:
	docker-compose stop $(container)
restart:
	docker-compose stop $(container)
	docker-compose up -d $(container)
logs:
	docker-compose logs --tail=100 -f $(container)
ps:
	docker-compose ps
cli:
	docker-compose exec app bash
shell:
	docker-compose exec $(container) sh
env:
	docker-compose run --rm app sh -c '[ ! -e ".env" ] && cp .env.example .env || echo 0'
install:
	docker-compose run --rm app sh -c 'composer install --no-dev --no-interaction --ansi'
install-dev:
	docker-compose run --rm app sh -c 'composer install --no-interaction --ansi'
migrate:
	docker-compose run --rm app sh -c 'php artisan migrate --force --no-interaction'
run: up-all env install migrate
run-dev: up-all env install-dev migrate
