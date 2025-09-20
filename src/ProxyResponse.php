<?php

namespace Polopolaw\ProxyManager;

/**
 * Объект ответа с данными прокси
 */
class ProxyResponse
{
    private $proxy;
    private $country;
    private $totalProxies;
    private $currentIndex;

    public function __construct(array $data)
    {
        $this->proxy = $data['proxy'] ?? '';
        $this->country = $data['country'] ?? 'default';
        $this->totalProxies = $data['total_proxies'] ?? 0;
        $this->currentIndex = $data['current_index'] ?? 0;
    }

    /**
     * Возвращает адрес прокси
     * 
     * @return string
     */
    public function getProxy(): string
    {
        return $this->proxy;
    }

    /**
     * Возвращает страну прокси
     * 
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Возвращает общее количество прокси в списке
     * 
     * @return int
     */
    public function getTotalProxies(): int
    {
        return $this->totalProxies;
    }

    /**
     * Возвращает текущий индекс в списке
     * 
     * @return int
     */
    public function getCurrentIndex(): int
    {
        return $this->currentIndex;
    }

    /**
     * Возвращает все данные в виде массива
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'proxy' => $this->proxy,
            'country' => $this->country,
            'total_proxies' => $this->totalProxies,
            'current_index' => $this->currentIndex
        ];
    }

    /**
     * Магический метод для получения свойств
     * 
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * Строковое представление объекта
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->proxy;
    }
}
