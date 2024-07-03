# Petshop API

Petshop API is a Laravel-based application for managing a pet shop's backend operations. It provides endpoints for managing categories, products, users, and more.

## Prerequisites

Before you start, ensure you have the following installed:

- Docker
- PHP version 8.3 or later
- Web browser
- Shell or terminal environment

## Getting Started

1. **Clone the repository:**

   ```bash
   git clone https://github.com/degod/petshop-api.git
   ```

2. **Navigate to the project directory:**

	```bash
	cd petshop-api/
	```

3. **Install Composer dependencies:**

	```bash
	composer install
	```

4. **Start the application with Laravel Sail:**

	```bash
	./vendor/bin/sail up -d
	```

5. **Logging in to container shell:**

	```bash
	./vendor/bin/sail root-shell
	```

6. **Completing the setup:**

	```bash
	php artisan migrate:fresh && php artisan db:seed && ./vendor/bin/pint --preset psr12 && ./vendor/bin/phpstan analyse && php artisan test
	```

7. **Exiting container shell:**

	```bash
	exit
	```

8. **Accessing the application:**

- The application should now be running on your local environment.
- Navigate to `http://petshop-api.test` in your browser to access the application.
- For API documentation, visit `http://petshop-api.test/api/documentation#/`.
- To access the database, go to `http://petshop-api.test:8001/`.

## Contributing

If you encounter bugs or wish to contribute, please follow these steps:

- Fork the repository and clone it locally.
- Create a new branch (`git checkout -b feature/fix-issue`).
- Make your changes and commit them (`git commit -am 'Fix issue'`).
- Push to the branch (`git push origin feature/fix-issue`).
- Create a new Pull Request against the `main` branch, tagging `@degod`.

## Contact

For inquiries or assistance, you can reach out to Godwin Uche:

- `Email:` degodtest@gmail.com
- `Phone:` +2348024245093


