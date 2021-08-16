# CSV Image Bulk Uploader

Simple application for bulk uploading images via CSV files.

The CSV header column names are ignored, but the functionality expects certain values for each one of the row columns as defined in the custom file definition in the codebase.

By default, the project runs in a dev environment which provides the Symfony Profiler toolbar to ease troubleshooting. Switching to prod is also possible by updating /symfony/.env.
## Installation

./docker/install

## User Guide

Upload Images\
http://localhost/image/upload

List all images \
http://localhost/image/list

Preview an image by its unique id\
http://localhost/image/preview/uniqueId

## Technical Details

The application uses the Symfony framework and the main functionality can be found below:
* symfony/src/Controller
* symfony/src/Service

The command below is always triggered after running the project with ./docker/install. However, should you need to use other docker-compose commands to restart the containers, don't forget to run:

docker-compose exec php sh -c "bin/console messenger:consume -vv"