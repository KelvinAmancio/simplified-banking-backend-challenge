build:
	docker compose build --no-cache
up:
	docker compose up
down:
	docker compose down
exec:
	docker compose exec main bash
mysql:
	docker compose exec mysql mysql -u my_user -pmy_password my_database
install:
	composer install
start:
	php /opt/www/bin/hyperf.php start
migrate:
	php bin/hyperf.php migrate
migrate_rollback:
	php bin/hyperf.php migrate:reset
coverage-u:
	export XDEBUG_MODE=coverage && vendor/bin/phpunit --testsuite u --coverage-html=test/coverage/unit
coverage-i:
	export XDEBUG_MODE=coverage && vendor/bin/phpunit --testsuite i --coverage-html=test/coverage/integration
coverage-a:
	export XDEBUG_MODE=coverage && vendor/bin/phpunit --coverage-html=test/coverage/all
