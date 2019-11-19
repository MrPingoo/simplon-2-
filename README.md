# Docker Compose for Symfony 4 

## Liste of services : 

> PHP 7.2
> NGINX
> Mailhog
> MariaDB

## How to install Docker on Linux : 

### Install Docker

> Go to https://docs.docker.com/install/linux/docker-ce/ubuntu/ and follow the instructions

### Change your project name : 

Go to .env file : 

> change the line 4 by your project name 
> PROJECT_NAME=docker-for-symfony-4

### Build your Docker :  

> cd /your-docker-project
> docker-compose build 

### Start your Docker :

> docker-compose up -d 

### Stop your Docker :

> docker-compose stop 

### Execute a command on your DB : 

> docker-compose exec db bash

## How to install Docker for Mac and Windows : 

### Install Docker

> Go to https://docs.docker.com/docker-for-mac/ and follow the instructions
> For Windows https://docs.docker.com/docker-for-windows/ and follow the instructions

### Install Docker Sync 

One of the most important problem with Docker on Mac and Windows is slowness. To solve this problem I use docker-sync. 
Docker-sync allow to run rsync command when a change is detected. 

> Go to https://docker-sync.readthedocs.io/en/latest/getting-started/installation.html#osx and follow the instructions for Mac
> Go to https://docker-sync.readthedocs.io/en/latest/getting-started/installation.html#windows and follow the instructions for Windows

### Change your project name : 

Go to .env file : 

> change the line 4 by your project name 
> PROJECT_NAME=docker-for-symfony-4

For Mac I created a Makefile to run Docker-sync with only one command : 
> change the projectname in the docker-sync.yml file, Makefile and docker-compose-dev.yml : 
> docker volume create --name=docker-for-symfony-4-sync in docker-compose-dev.yml
> docker-for-symfony-4-sync in docker-sync.yml 
> docker-for-symfony-4-sync in Makefile

### Build your Docker :  

> cd /your-docker-project
> docker-compose build 

### Start your Docker :

> On Mac you can use : 
> Make start_dev

> On Windows you can use : 
> docker-sync start

### Stop your Docker :

> On Mac you can use : 
> Make stop_dev

> On Windows you can use : 
> docker-sync stop

### Execute a command on your DB : 

> docker-compose exec db bash

## Troubles : 

### If you have troubles with your DB : 

```
docker exec -it db bash
mysql -u root -p
mysql> SET GLOBAL innodb_fast_shutdown = 1;
mysql_upgrade -u root -p
```

### If Docker Sync still not working 

> docker-sync clean
or 
> docker-sync sync