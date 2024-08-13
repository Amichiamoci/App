# Amichiamoci App
[![Docker image](https://github.com/Amichiamoci/App/actions/workflows/docker-build.yml/badge.svg)](https://github.com/Amichiamoci/App/actions/workflows/docker-build.yml)

### Testa localmente con Docker
Copia la repo
```bash
git clone https://github.com/Amichiamoci/App && cd App
```

Esegui
```bash
docker build . --tag 'amichiamoci-app'
```
E dopo
```bash
cd demo && docker-compose down && docker-compose up -d && cd ..
```
L'ultimo comando avvierà anche un'istanza di phpMyAdmni sulla porta 8080