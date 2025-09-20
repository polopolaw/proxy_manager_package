# Proxy Manager Client

PHP клиент для работы с Proxy Manager API. Позволяет легко получать прокси-серверы с ротацией по кругу и фильтрацией по странам.

## Установка

### Через приватный репозиторий

1. Добавьте репозиторий в ваш `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/your-username/proxy-manager-client.git"
        }
    ],
    "require": {
        "polopolaw/proxy-manager-client": "^1.0"
    }
}
```

2. Установите пакет:

```bash
composer install
```

### Через локальный путь (для разработки)

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./path/to/proxy-manager-package"
        }
    ],
    "require": {
        "polopolaw/proxy-manager-client": "*"
    }
}
```

## Использование

### Базовое использование

```php
<?php

use Polopolaw\ProxyManager\ProxyManagerClient;
use Polopolaw\ProxyManager\ProxyManagerException;

// Создаем клиент
$client = new ProxyManagerClient(
    'https://your-proxy-server.com/proxy_manager.php', // URL вашего сервера
    'admin',                                           // логин
    'password123'                                      // пароль
);

try {
    // Получаем следующий прокси из общего списка
    $proxy = $client->getProxy();
    echo "Прокси: " . $proxy->getProxy() . "\n";
    echo "Страна: " . $proxy->getCountry() . "\n";
    echo "Всего прокси: " . $proxy->getTotalProxies() . "\n";
    
} catch (ProxyManagerException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
```

### Получение прокси по стране

```php
try {
    // Получаем прокси для конкретной страны
    $proxy = $client->getProxyByCountry('usa');
    echo "USA прокси: " . $proxy->getProxy() . "\n";
    
    $proxy = $client->getProxyByCountry('germany');
    echo "Germany прокси: " . $proxy->getProxy() . "\n";
    
} catch (ProxyManagerException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
```

### Отладочные функции

```php
try {
    // Получить текущее состояние индексов
    $state = $client->getState();
    print_r($state);
    
    // Сбросить индексы (начинать с начала)
    $result = $client->resetIndices();
    echo $result['message'] . "\n";
    
} catch (ProxyManagerException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
```

### Настройка HTTP клиента

```php
$client = new ProxyManagerClient(
    'https://your-proxy-server.com/proxy_manager.php',
    'admin',
    'password123',
    [
        'timeout' => 60,           // таймаут запроса
        'connect_timeout' => 30,   // таймаут подключения
        'verify' => false,         // отключить проверку SSL
        'headers' => [
            'User-Agent' => 'MyApp/1.0'
        ]
    ]
);
```

## API Reference

### ProxyManagerClient

#### Конструктор
```php
public function __construct(string $baseUrl, string $login, string $password, array $options = [])
```

#### Методы

- `getProxy()` - Получить следующий прокси из общего списка
- `getProxyByCountry(string $country)` - Получить прокси для конкретной страны
- `resetIndices()` - Сбросить индексы (для отладки)
- `getState()` - Получить текущее состояние индексов

### ProxyResponse

#### Методы

- `getProxy()` - Адрес прокси
- `getCountry()` - Страна прокси
- `getTotalProxies()` - Общее количество прокси
- `getCurrentIndex()` - Текущий индекс
- `toArray()` - Все данные в виде массива

### ProxyManagerException

Исключение, которое выбрасывается при ошибках API.

## Требования

- PHP >= 7.4
- GuzzleHttp/Guzzle ^7.0

## Лицензия

MIT
