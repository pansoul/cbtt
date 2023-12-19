env:
	docker exec cbtt_php /bin/sh -c "cp -fr .env.example .env"

start:
	docker compose -p cbtt up

start_rebuild:
	docker compose -p cbtt up --build

stop:
	docker compose -p cbtt down

restart:
	make stop
	make start

migrate_fresh:
	docker exec cbtt_php php artisan migrate:fresh --seed

run_storekeeper_worker:
	docker exec cbtt_php php artisan rabbitmq:storekeeper-worker

run_handler_worker:
	docker exec cbtt_php php artisan rabbitmq:handler-worker
