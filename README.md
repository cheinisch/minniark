# Minniark

> [!IMPORTANT]
> This CMS is under development

Minniark is a lightweight, flat-file-based portfolio template for showcasing images and projects — no database required, easy to customize, fully responsive.

## Demo

There is a demo installation under [demo](https://demo.image-portfolio.org)

## Installation

### Docker Container

You can also install Minniark via Docker. The easiest way to do this is with the following commands:
```
mkdir minniark
cd minniark
curl -o docker-compose.yml https://raw.githubusercontent.com/cheinisch/minniark/refs/heads/main/docker/docker-compose.yml
docker-compose up -d
```

Once the container has been deployed, it can be accessed at `http://localhost:8080`. The port can be changed in docker-compose.yml.

You can find more details about the Docker installation at [https://minniark.app](https://minniark.app).

If you want to customize the docker-compose yourself, you can find it in the repo under /docker.

### Manual Installation

1. Download the latest Version and upload it into your Webserver.
2. Follow the instructions from the installer.
3. That's it.

## Links

- [Documentation](https://minniark.app)

## Documentation

Take a look at the [documentation](https://minniark.app) to get started with Image-Portfolio.

## Build Status

### Docker Images

| Image | Stable |
|---|---|
| Apache + PHP | [![.github/workflows/docker-build-apache.yml](https://github.com/cheinisch/minniark/actions/workflows/docker-build-apache.yml/badge.svg?branch=main)](https://github.com/cheinisch/minniark/actions/workflows/docker-build-apache.yml) |
| Nginx | [![.github/workflows/docker-build-nginx-app.yml](https://github.com/cheinisch/minniark/actions/workflows/docker-build-nginx-app.yml/badge.svg?branch=main)](https://github.com/cheinisch/minniark/actions/workflows/docker-build-nginx-app.yml) |
| PHP for Nginx | [![.github/workflows/docker-build-php.yml](https://github.com/cheinisch/minniark/actions/workflows/docker-build-php.yml/badge.svg?branch=main)](https://github.com/cheinisch/minniark/actions/workflows/docker-build-php.yml) |

## Technologies Used

This project uses the following technologies:

- [Tailwind CSS](https://tailwindcss.com/) – for utility-first, modern CSS styling
- [Twig](https://twig.symfony.com/) – a flexible and fast templating engine for PHP
- [YAML](https://symfony.com/packages/Yaml)
- [GLightBox](https://github.com/biati-digital/glightbox) - a small lightbox for the pictures
- [Parsedown](https://github.com/erusev/parsedown) - parser for markdown
---

© 2021-2025 [Christian Heinisch](https://heimfisch.de)  
Released under the [MIT license](https:/minniark.app/license)
