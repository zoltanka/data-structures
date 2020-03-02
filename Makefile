# Setting up variables
.DEFAULT_GOAL := help
green   =\033[32m
rws     =\033[31m
yellow  =\033[33m
blue    =\033[34m
white   =\033[0m

bold   =$(shell tput bold)
normal =${shell tput sgr0}
help:
	@echo ""
	@echo "${y}$(bold)Usage:$(normal)"
	@echo "  ${white}make ${green}target${white} ${blue}[arguments]${white}"
	@echo ""
	@echo "${yellow}$(bold)Available targets:$(normal)"
	@echo "  ${green}build${white}                 Builds docker image to run unit tests"
	@echo "  ${green}run-test${white}              Builds the docker image, then runs the unit tests"
	@echo "    ${blue}local${white}               Mounts the current code instead of clean run"
	@echo "    ${blue}no-build${white}            Uses the already built image"
	@echo ""

build:
	@echo "Rebuilding unit test image ${nobuild}"
	docker build -t zfekete/data-structures ./

run-test-deploy:
	@echo "Executing unit tests..."
	docker-compose -f docker-compose-deploy.yml up
run-test-dev:
	@echo "Executing unit tests..."
	docker-compose -f docker-compose-dev.yml up

ifeq ($(no-build),true)
run-test: run-test-deploy
else
run-test: build run-test-deploy
endif