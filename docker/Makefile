include ../.env
export $(shell sed 's/=.*//' ../.env)

USER_ID:=$(shell id -u)
GROUP_ID:=$(shell id -g)
export UID = $(USER_ID)
export GID = $(GROUP_ID)
DOCKER_COMPOSE = cd .. && docker-compose
DOCKER = cd .. && docker
USER_DOCKER = $(USER_ID):$(GROUP_ID)
DOCKER_PHP = $(DOCKER) exec -u $(USER_DOCKER) -it $(CONTAINER_NAME)_php-fpm sh -c
DOCKER_NPM = $(DOCKER) exec -u $(USER_DOCKER) -it $(CONTAINER_NAME)_nodejs sh -c
SYMFONY = $(DOCKER_PHP) "php bin/console ${ARGS}"

##
## ALIAS
## -------
##

ex: xdebug-enable
dx: xdebug-disable
exr: xdebug-enable restart-php-fpm
dxr: xdebug-disable restart-php-fpm
cc: cache-clear
asset-dev: npm-dev
watch: npm-watch
asset-prod: npm-prod
sf-cmd: symfony-cmd
pp: post-pull
build: asset-prod
rebuild-all-db: rebuild-dev-db rebuild-test-db
reset-db: rebuild-all-db
qq: quick_qa


post-pull:
	make vendor
	make cc ENV=test
	make node_modules
	make asset-dev
	make htaccess-update
	make security-check

##
## Makefile Help
## -------
##
.DEFAULT_GOAL := help

help: ## Default goal (display the help message)
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.PHONY: help

##
## Docker
## -------
##
start: ## Start environnement docker.
start: ../docker-compose.yml
	UID=$(USER_ID) GID=$(GROUP_ID) $(DOCKER_COMPOSE) up -d --build

init: ## Initialize project
init:
	make start
	make dx
	make exec-php ARGS="composer install --no-scripts"
	make node_modules
	make exec-php ARGS="composer run auto-scripts -n"
	make asset-dev
	make rebuild-all-db
	make htaccess-update
	make asset-dev

destroy: ## Destroy all containers & network
destroy:
	$(DOCKER_COMPOSE) down

destroy-all: ## Destroy all containers, network, and known data and vendor folders
destroy-all:
	make exec-php ARGS="rm -rf vendor node_modules public/var public/var public/assets/build public/assets/ezplatform/build public/assets/translations/* public/.htaccess db-signature_dev.json db-signature_test.json var"
	make destroy

stop: ## Stop all containers
stop:
	$(DOCKER_COMPOSE) stop

list-containers: ## List container docker
list-containers:
	$(DOCKER_COMPOSE) ps

list-network: ## List all networks on host
list-network:
	$(DOCKER) network ls

inspect-network: ## Inspect current network to list all container ips
inspect-network:
	$(DOCKER) network inspect $(CONTAINER_NAME)

erase-all: ## Careful, erase all container, all images
erase-all:
	$(DOCKER) stop $$(docker ps -a -q) && $(DOCKER) rm $$(docker ps -a -q) $(DOCKER) rmi $$(docker images -a -q) -f

exec-php: ## Exec command inside container php. Use argument ARGS
exec-php:
	$(DOCKER_PHP) "${ARGS}"

exec-node: ## Exec command inside container nodejs. Use argument ARGS
exec-node:
	$(DOCKER_NPM) "${ARGS}"

connect-php: ## Connect sh to container php
connect-php:
	$(DOCKER) exec -u $(USER_DOCKER) -it $(CONTAINER_NAME)_php-fpm sh

connect-php-root: ## Connect sh to container as user root
connect-php-root:
	$(DOCKER) exec -it $(CONTAINER_NAME)_php-fpm sh

connect-node: ## Connect sh to container nodejs
connect-node:
	$(DOCKER) exec -u $(USER_DOCKER) -it $(CONTAINER_NAME)_nodejs sh

stop-all: ## Stop all containers
stop-all:
	$(DOCKER) stop $$(docker ps -a -q)

##
## Manage dependencies
## -------
##
vendor: ## Install composer dependencies
vendor: ../composer.lock
	$(DOCKER_PHP) "composer install"

require-vendor: ## Add dependency or dev dependency. Use argument ARGS (Example : make new-vendor ARGS="security form" or make new-vendor ARGS="profiler --dev"
require-vendor:
	$(DOCKER_PHP) "composer require ${ARGS}"

update-vendor: ## Update dependencies
update-vendor:
	$(DOCKER_PHP) "composer update ${ARGS}"

node_modules: ## Install npm dependencies
node_modules:
	$(DOCKER_NPM) "yarn install ${ARGS}"

new-node_modules: ## Add dependency or dev dependency for npm usage. Use argument ARGS (Example : make new-node_modules ARGS="bootstrap --save") or with --save-dev
new-node_modules: ../package.json
	$(DOCKER_NPM) "yarn add ${ARGS}"

dump-autoload: ## Optimize autoloading and vendor
dump-autoload: ../composer.lock
	$(DOCKER_PHP) "composer dump-autoload"

##
## Symfony Command
## -------
##
symfony-cmd: ## Exec command symfony inside php container. Use argument ARGS to define command. Example : make symfony-cmd ARGS="assets:install"
symfony-cmd:
	$(SYMFONY)

cache-clear: ## Clear the cache (by default, the dev env is used, use ARGS argument to change)
cache-clear:
	$(DOCKER_PHP) "php bin/console cache:clear --siteaccess=$(or $(SA), admin) ${ARGS}"

##
## Tools
## -------
##
xdebug-enable: ## Enable Xdebug
xdebug-enable:
	$(DOCKER) exec -it $(CONTAINER_NAME)_php-fpm sh -c "cp -f /usr/local/etc/php/conf.d/xdebug.ini.disabled /usr/local/etc/php/conf.d/xdebug.ini"
	$(DOCKER) exec -it $(CONTAINER_NAME)_php-fpm bash -c 'kill -USR2 $$(pidof -s php-fpm)'

xdebug-disable: ## Disable Xdebug
xdebug-disable:
	$(DOCKER) exec -it $(CONTAINER_NAME)_php-fpm sh -c "rm -f /usr/local/etc/php/conf.d/xdebug.ini"
	$(DOCKER) exec -it $(CONTAINER_NAME)_php-fpm bash -c 'kill -USR2 $$(pidof -s php-fpm)'

restart-php-fpm: ## Restart php fpm
restart-php-fpm:
	$(DOCKER) restart $(CONTAINER_NAME)_php-fpm

npm-prod: ## Build npm for production environment
npm-prod: ../package.json
	$(DOCKER_NPM) "yarn run build"

npm-watch: ## Build npm for watch
npm-watch: ../package.json
	$(DOCKER_NPM) "yarn run watch"

npm-dev: ## Build npm for dev environment
npm-dev:
	$(DOCKER_NPM) "yarn run dev"

##
## APP SPECIFIC
## -------
##
rebuild-test-db:
	make symfony-cmd ARGS="doctrine:database:drop --force --env=test"
	make symfony-cmd ARGS="ibexa:install ibexa-oss --env=test"

rebuild-dev-db:
	make symfony-cmd ARGS="doctrine:database:drop --force"
	make symfony-cmd ARGS="ibexa:install ibexa-oss"

security-check:
ifeq (, $(shell command -v local-php-security-checker &> /dev/null))
	echo 'To check security, install local-php-security-checker here : https://github.com/fabpot/local-php-security-checker'
else
	cd .. && local-php-security-checker && cd docker
endif

htaccess-update:
	cp ../public/.htaccess.dev ../public/.htaccess

clean:
	rm -rf ../var/cache/dev ../var/cache/test ../var/log/dev.log ../var/log/test.log

test:
	$(DOCKER_PHP) "composer test"

quick_qa:
	$(DOCKER_PHP) "composer quick_qa"

behat:
	$(DOCKER_PHP) "vendor/bin/behat --stop-on-failure $(ARGS)"

behat-tags:
	$(DOCKER_PHP) "vendor/bin/behat --tags=$(TAGS) --stop-on-failure $(ARGS)"
