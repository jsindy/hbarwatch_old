#!/bin/bash
docker build -t hbarwatch:latest .
docker rm -f hbarwatch
docker run -d -p 80:80 --restart unless-stopped --name hbarwatch hbarwatch:latest
