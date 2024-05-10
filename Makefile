build:
	docker compose build --no-cache
up:
	docker compose up
down:
	docker compose down
exec:
	docker compose exec main bash
install:
	composer install
start:
	php /opt/www/bin/hyperf.php start
migrate:
	php bin/hyperf.php migrate