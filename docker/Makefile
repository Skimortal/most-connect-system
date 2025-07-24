.PHONY: up

up:
	docker-compose build
	docker-compose up -d
	./setup.sh
	./update.sh
	./init-db.sh
