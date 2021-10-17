.PHONY: composer

composer-install: ## Run composer install
	docker run --rm -it -v $(PWD):/app composer install

composer-update: ## Run composer update
	docker run --rm -it -v $(PWD):/app composer update
