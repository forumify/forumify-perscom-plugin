.PHONY: quality
quality:
	@./vendor/bin/phpcs
	@./vendor/bin/phpstan

.PHONY: quality-fix
quality-fix:
	@./vendor/bin/phpcbf
