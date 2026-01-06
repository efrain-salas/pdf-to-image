#!/bin/sh
docker build --platform linux/amd64 -t efrainsalas/pdf-to-image .
docker push efrainsalas/pdf-to-image
