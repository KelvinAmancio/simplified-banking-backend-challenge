build:
	docker compose build --no-cache
up:
	docker compose up
down:
	docker compose down
exec:
	docker compose exec main bash
migrate:
	docker compose run main bash php bin/hyperf.php migrate