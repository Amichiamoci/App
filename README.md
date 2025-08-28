![Logo di Amichiamoci](./assets/logos/logo.png "Logo di Amichiamoci")

# Amichiamoci App
[![Docker image](https://github.com/Amichiamoci/App/actions/workflows/docker-build.yml/badge.svg)](https://github.com/Amichiamoci/App/actions/workflows/docker-build.yml)

Questo è il codice sorgente della [App di Amichiamoci](https://app.amichiamoci.it "Vai alla App"), disponibile come repository ed anche come immagine docker.

## Scaricare con Docker
```bash
docker pull gchr.io/amichiamoci/app:latest
```

### Testa localmente con Docker
Clona la repo
```bash
git clone https://github.com/Amichiamoci/App && cd App
```

Ed esegui
```bash
cd demo && docker-compose up -d --build && cd ..
```

## Costruire con composer
Clona la repo
```bash
git clone https://github.com/Amichiamoci/App && cd App
```

Scarica le librerie
```bash
composer update --no-interaction --no-progress
```

Scarica i moduli node e compila gli asset
```bash
php bin/console importmap:install && php bin/console asset-map:compile
```

Riempi il file `.env` con le variabili necessarie