
# CanaryAAC

CanaryAAC is a free and open-source Automatic Account Creator (AAC) written in MVC-PHP. It supports only MySQL databases.


## Information

- Fully Object Oriented
- Model/View/Controller (MVC)
- Middlewares
- API
- Composer
    - Fast Route
    - PhpDotEnv
    - Twig
    - Google2FA
    - GuzzleHttp
    - DiscordPHP
    - PagSeguro
    - PayPal
    - MercadoPago
    - Carbon
- Using .env to configure

## Installation

**Install CanaryAAC on Debian / Ubuntu**

```bash
  sudo apt install php-bcmath
  sudo apt install php-curl
  sudo apt install php-dom
  sudo apt install php-gd
  sudo apt install php-mbstring
  sudo apt install php-mysql
  sudo apt install php-pdo
  sudo apt install php-xml
  sudo apt install php-json
```    
## Configure

- Import canaryaac.sql to your existing OpenTibia database
-  Configure .env to suit your server
## API Documentation

#### Search Characters

```http
  POST /api/v1/searchcharacter
```

| Parameter   | Type       | Description                         |
| :---------- | :--------- | :---------------------------------- |
| `name` | `string` | Character Search |

#### Client Login

```http
  POST /api/v1/login
```

| Parameter   | Type       | Description                                 |
| :---------- | :--------- | :------------------------------------------ |
| `request`      | `string` | Connection to the client. |

## Autor

- [@lucasgiovannibr](https://www.github.com/lucasgiovannibr)

