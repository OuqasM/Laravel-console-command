# Laravel Project Setup with Laravel Sail
=============================================

A command console that imports data from several different sources such as CSV/Api based on what you provided in the command

Prerequiites
-------------

* Docker must be installed and running on your system. For more information, visit [Docker's official website](https://www.docker.com/).

Steps to Set Up the Project
---------------------------

### 1. Clone the Project

Clone the project repository:

    git clone https://github.com/OuqasM/Laravel-console-command.git
    cd Laravel-console-command

### 2. Install Dependencies

    composer install

### 3. Configure Environment

Copy the `.env.example` file to `.env` and configure the database settings based on the configuration in the `docker-compose.yaml` file.

### 4. Generate Application Key

    ./vendor/bin/sail artisan key:generate

### 5. Start Docker Containers

    ./vendor/bin/sail up --build

### 6. Run Database Migrations

    ./vendor/bin/sail artisan migrate

### 7. Run the Command

    ./vendor/bin/sail artisan import:products api

or

    ./vendor/bin/sail artisan import:products csv
    # make sure you put the file products.csv on Storage/app directory
