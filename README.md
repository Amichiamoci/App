![Logo di Amichiamoci](./assets/logos/logo.png "Logo di Amichiamoci")

# Amichiamoci App
[![Docker image](https://github.com/Amichiamoci/App/actions/workflows/docker-build.yml/badge.svg)](https://github.com/Amichiamoci/App/actions/workflows/docker-build.yml)

Questo è il codice sorgente della [App di Amichiamoci](https://app.amichiamoci.it "Vai alla App"), disponibile come repository ed anche come immagine docker.

## Scaricare con Docker
```bash
docker pull ghcr.io/amichiamoci/app:latest
```

### Testa localmente con Docker
Clona la repo
```bash
git clone https://github.com/Amichiamoci/App && cd App
```

Ed esegui
```bash
docker-compose up -d --build
```
