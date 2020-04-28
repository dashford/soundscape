composer :
	docker-compose exec php sh -c "composer validate && composer install"

doctrine-cli :
	docker-compose exec php sh -c "php ./cli-config.php"