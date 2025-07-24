.PHONY: up

up:
	docker-compose build
	docker-compose up -d
	./setup.sh
	./update.sh
	./init-db.sh

up-prod:
	docker-compose -f docker-compose.prod.yml build
	docker-compose -f docker-compose.prod.yml up -d
	./setup.sh
	./update.sh
	./init-db.sh
