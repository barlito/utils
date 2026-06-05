stack_name=utils

# Container
app_container_id = $(shell docker ps --filter name="$(stack_name)" -q)

.PHONY: bash
bash:
	docker exec -it -u root $(app_container_id) bash

.PHONY: deploy
deploy:
	docker compose up -d

.PHONY: undeploy
undeploy:
	docker compose down

.PHONY: restart
restart:
	make undeploy
	make deploy

.PHONY: logs
logs:
	docker compose logs -f

.PHONY: phpstan
phpstan:
	docker exec $(app_container_id) composer phpstan

.PHONY: rector
rector:
	docker exec $(app_container_id) composer rector

.PHONY: rector-fix
rector-fix:
	docker exec $(app_container_id) composer rector:fix

.PHONY: cs
cs:
	docker exec $(app_container_id) composer cs

.PHONY: cs-fix
cs-fix:
	docker exec $(app_container_id) composer cs:fix

.PHONY: phpcs
phpcs:
	docker exec $(app_container_id) composer phpcs

.PHONY: phpmd
phpmd:
	docker exec $(app_container_id) composer phpmd

.PHONY: qa
qa:
	docker exec $(app_container_id) composer qa
