<?php

namespace Polopolaw\ProxyManager;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Клиент для работы с Proxy Manager API
 */
class ProxyManagerClient
{
    private $client;
    private $baseUrl;
    private $login;
    private $password;

    /**
     * @param string $baseUrl URL вашего proxy manager сервера
     * @param string $login Логин для авторизации
     * @param string $password Пароль для авторизации
     * @param array $options Дополнительные опции для GuzzleHttp\Client
     */
    public function __construct(string $baseUrl, string $login, string $password, array $options = [])
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->login = $login;
        $this->password = $password;
        
        $defaultOptions = [
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];
        
        $this->client = new Client(array_merge($defaultOptions, $options));
    }

    /**
     * Получает следующий прокси из общего списка
     * 
     * @return ProxyResponse
     * @throws ProxyManagerException
     */
    public function getProxy(): ProxyResponse
    {
        return $this->makeRequest();
    }

    /**
     * Получает следующий прокси для конкретной страны
     * 
     * @param string $country Код страны (например: 'usa', 'germany', 'russia')
     * @return ProxyResponse
     * @throws ProxyManagerException
     */
    public function getProxyByCountry(string $country): ProxyResponse
    {
        return $this->makeRequest($country);
    }

    /**
     * Сбрасывает индексы прокси (для отладки)
     * 
     * @return array
     * @throws ProxyManagerException
     */
    public function resetIndices(): array
    {
        return $this->makeRequest(null, 'reset');
    }

    /**
     * Получает текущее состояние индексов (для отладки)
     * 
     * @return array
     * @throws ProxyManagerException
     */
    public function getState(): array
    {
        return $this->makeRequest(null, 'state');
    }

    /**
     * Выполняет запрос к API
     * 
     * @param string|null $country
     * @param string|null $action
     * @return mixed
     * @throws ProxyManagerException
     */
    private function makeRequest(?string $country = null, ?string $action = null)
    {
        $data = [
            'login' => $this->login,
            'password' => $this->password
        ];

        if ($country !== null) {
            $data['country'] = $country;
        }

        if ($action !== null) {
            $data['action'] = $action;
        }

        try {
            $response = $this->client->post($this->baseUrl, [
                'json' => $data
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ProxyManagerException('Ошибка декодирования JSON ответа: ' . json_last_error_msg());
            }

            if (!$body['success']) {
                throw new ProxyManagerException($body['error'], $body['code'] ?? 400);
            }

            // Для команд reset и state возвращаем данные напрямую
            if ($action !== null) {
                return $body['data'];
            }

            // Для получения прокси возвращаем объект ProxyResponse
            return new ProxyResponse($body['data']);

        } catch (RequestException $e) {
            $message = 'Ошибка HTTP запроса: ' . $e->getMessage();
            if ($e->hasResponse()) {
                $body = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($body, true);
                if ($errorData && isset($errorData['error'])) {
                    $message = $errorData['error'];
                }
            }
            throw new ProxyManagerException($message, $e->getCode());
        } catch (GuzzleException $e) {
            throw new ProxyManagerException('Ошибка Guzzle: ' . $e->getMessage(), $e->getCode());
        }
    }
}
