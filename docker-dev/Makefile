.PHONY: up

up:
	docker-compose build
	docker-compose up -d
	bin/setup.sh
	bin/update.sh
	bin/init-db.sh
