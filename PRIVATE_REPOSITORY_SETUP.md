# Настройка приватного репозитория

## Варианты размещения приватного пакета

### 1. GitHub Private Repository (Рекомендуется)

1. **Создайте приватный репозиторий на GitHub:**
   ```bash
   # В папке proxy-manager-package
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/your-username/proxy-manager-client.git
   git push -u origin main
   ```

2. **Создайте Personal Access Token:**
   - Идите в GitHub → Settings → Developer settings → Personal access tokens
   - Создайте токен с правами `repo` (для приватных репозиториев)
   - Скопируйте токен

3. **Настройте Composer для работы с приватным репозиторием:**
   ```bash
   composer config github-oauth.github.com YOUR_TOKEN_HERE
   ```

4. **В ваших проектах добавьте репозиторий:**
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

### 2. GitLab Private Repository

1. **Создайте приватный проект на GitLab**
2. **Загрузите код:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://gitlab.com/your-username/proxy-manager-client.git
   git push -u origin main
   ```

3. **Настройте Composer:**
   ```bash
   composer config gitlab-oauth.gitlab.com YOUR_TOKEN_HERE
   ```

4. **В проектах:**
   ```json
   {
       "repositories": [
           {
               "type": "vcs",
               "url": "https://gitlab.com/your-username/proxy-manager-client.git"
           }
       ],
       "require": {
           "polopolaw/proxy-manager-client": "^1.0"
       }
   }
   ```

### 3. Bitbucket Private Repository

1. **Создайте приватный репозиторий на Bitbucket**
2. **Загрузите код аналогично GitHub/GitLab**
3. **Настройте Composer:**
   ```bash
   composer config bitbucket-oauth.bitbucket.org YOUR_KEY YOUR_SECRET
   ```

### 4. Локальный репозиторий (для разработки)

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

## Обновление пакета

1. **Внесите изменения в код**
2. **Обновите версию в composer.json:**
   ```json
   {
       "version": "1.0.1"
   }
   ```

3. **Создайте тег:**
   ```bash
   git add .
   git commit -m "Version 1.0.1"
   git tag v1.0.1
   git push origin main --tags
   ```

4. **В ваших проектах обновите:**
   ```bash
   composer update polopolaw/proxy-manager-client
   ```

## Безопасность

- **Никогда не коммитьте токены доступа в репозиторий**
- **Используйте переменные окружения для чувствительных данных**
- **Регулярно обновляйте токены доступа**
- **Ограничьте права токенов только необходимыми репозиториями**

## Troubleshooting

### Ошибка аутентификации
```bash
# Проверьте настройки токена
composer config --list | grep oauth

# Переустановите токен
composer config github-oauth.github.com YOUR_NEW_TOKEN
```

### Ошибка "Package not found"
- Убедитесь, что репозиторий добавлен в composer.json
- Проверьте правильность URL репозитория
- Убедитесь, что у токена есть доступ к репозиторию

### Ошибка версии
- Убедитесь, что создали тег с правильной версией
- Проверьте, что тег загружен в удаленный репозиторий
