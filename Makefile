composer-install :
	docker-compose exec php sh -c "composer validate && composer install"

doctrine-cli :
	docker-compose exec php sh -c "vendor/bin/doctrine $(command)"