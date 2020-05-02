up :
	docker-compose up

composer-install :
	docker-compose exec php sh -c "composer validate && composer install"

doctrine-cli :
	docker-compose exec php sh -c "vendor/bin/doctrine $(command)"

update-db-schema :
	docker-compose exec php sh -c "vendor/bin/doctrine orm:schema-tool:update --force --dump-sql"