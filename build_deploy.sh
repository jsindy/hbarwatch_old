#!/bin/bash
docker build . --file Dockerfile --tag hbarwatch:dev-$(date +%s) --tag hbarwatch:dev-latest
docker rm -f hbarwatch
docker run -d -p 80:80 --restart unless-stopped --name hbarwatch hbarwatch:dev-latest