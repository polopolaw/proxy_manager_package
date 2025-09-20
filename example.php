<?php

require_once 'vendor/autoload.php';

use Polopolaw\ProxyManager\ProxyManagerClient;
use Polopolaw\ProxyManager\ProxyManagerException;

// Настройки
$baseUrl = 'https://your-proxy-server.com/proxy_manager.php';
$login = 'admin';
$password = 'password123';

// Создаем клиент
$client = new ProxyManagerClient($baseUrl, $login, $password);

echo "=== Пример использования Proxy Manager Client ===\n\n";

try {
    // 1. Получаем прокси из общего списка
    echo "1. Получение прокси из общего списка:\n";
    $proxy = $client->getProxy();
    echo "   Прокси: {$proxy->getProxy()}\n";
    echo "   Страна: {$proxy->getCountry()}\n";
    echo "   Всего прокси: {$proxy->getTotalProxies()}\n";
    echo "   Текущий индекс: {$proxy->getCurrentIndex()}\n\n";

    // 2. Получаем еще один прокси (следующий в списке)
    echo "2. Следующий прокси:\n";
    $proxy2 = $client->getProxy();
    echo "   Прокси: {$proxy2->getProxy()}\n\n";

    // 3. Получаем прокси для конкретной страны
    echo "3. Получение прокси по странам:\n";
    
    $countries = ['usa', 'germany', 'russia'];
    foreach ($countries as $country) {
        try {
            $countryProxy = $client->getProxyByCountry($country);
            echo "   {$country}: {$countryProxy->getProxy()}\n";
        } catch (ProxyManagerException $e) {
            echo "   {$country}: Ошибка - {$e->getMessage()}\n";
        }
    }
    echo "\n";

    // 4. Показываем текущее состояние
    echo "4. Текущее состояние индексов:\n";
    $state = $client->getState();
    foreach ($state as $key => $value) {
        echo "   {$key}: {$value}\n";
    }
    echo "\n";

    // 5. Демонстрация работы с массивом данных
    echo "5. Данные прокси в виде массива:\n";
    $proxyArray = $proxy->toArray();
    print_r($proxyArray);

} catch (ProxyManagerException $e) {
    echo "Ошибка: {$e->getMessage()}\n";
    echo "Код ошибки: {$e->getErrorCode()}\n";
} catch (Exception $e) {
    echo "Неожиданная ошибка: {$e->getMessage()}\n";
}
