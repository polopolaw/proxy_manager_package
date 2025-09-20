<?php

namespace Polopolaw\ProxyManager;

/**
 * Клиент для работы с Proxy Manager API
 */
class ProxyManagerClient
{
    private $baseUrl;
    private $login;
    private $password;
    private $timeout;
    private $connectTimeout;
    private $headers;

    /**
     * @param string $baseUrl URL вашего proxy manager сервера
     * @param string $login Логин для авторизации
     * @param string $password Пароль для авторизации
     * @param array $options Дополнительные опции для HTTP клиента
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
        
        $mergedOptions = array_merge($defaultOptions, $options);
        $this->timeout = $mergedOptions['timeout'];
        $this->connectTimeout = $mergedOptions['connect_timeout'];
        $this->headers = $mergedOptions['headers'];
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
            $response = $this->makeHttpRequest($this->baseUrl, $data);
            $body = json_decode($response, true);

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

        } catch (ProxyManagerException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ProxyManagerException('Ошибка HTTP запроса: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Выполняет HTTP POST запрос с использованием cURL
     * 
     * @param string $url
     * @param array $data
     * @return string
     * @throws ProxyManagerException
     */
    private function makeHttpRequest(string $url, array $data): string
    {
        if (!extension_loaded('curl')) {
            throw new ProxyManagerException('Расширение cURL не установлено');
        }

        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_HTTPHEADER => $this->formatHeaders(),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_USERAGENT => 'ProxyManagerClient/1.0'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($response === false) {
            throw new ProxyManagerException('Ошибка cURL: ' . $error);
        }

        if ($httpCode >= 400) {
            $errorData = json_decode($response, true);
            $message = 'HTTP ошибка ' . $httpCode;
            if ($errorData && isset($errorData['error'])) {
                $message = $errorData['error'];
            }
            throw new ProxyManagerException($message, $httpCode);
        }

        return $response;
    }

    /**
     * Форматирует заголовки для cURL
     * 
     * @return array
     */
    private function formatHeaders(): array
    {
        $formattedHeaders = [];
        foreach ($this->headers as $key => $value) {
            $formattedHeaders[] = $key . ': ' . $value;
        }
        return $formattedHeaders;
    }
}
