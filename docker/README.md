# Paypro Docker for Opencart3

base image: [Bitname/Opencart](https://hub.docker.com/r/bitnami/opencart/)

## Requirements

- ports `80`, `3306` are available
- [docker-compose](https://docs.docker.com/compose/install/)

## Start docker

    docker-compose up -d

After it is installed open opencart [opencart - http://localhost](http://localhost:80)

## Variables

- Admin page: [http://localhost/admin](http://localhost:80/admin)
- Admin user: `paypro`
- Admin password: `admin`
- Admin email: `admin@paypro.nl`

- Database name: `paypro_opencart`
- Database user: `paypro_opencart`
- Database password: `paypro`

## Installing Paypro easy-digital-downloads-payments-plugin

If you haven't already clone/download this repository.

open the terminal in the cloned/downloaded repository

### copy all the content of `upload` into the container

    docker cp <path-to-upload>/upload/. <container-name OR container-id>:/opt/bitnami/opencart
