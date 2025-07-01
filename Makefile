.PHONY: quality
quality:
	@./vendor/bin/phpcs -s -p
	@./vendor/bin/phpstan

.PHONY: quality-fix
quality-fix:
	@./vendor/bin/phpcbf
