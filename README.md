# Laravel Users + UserImages Project

Проект на Laravel, который реализует:

- Модель `User` с полями `id`, `name`, `city`, `created_at`
- Модель `UserImage` с полями `id`, `user_id`, `image`, `created_at`
- API для получения пользователей с подсчётом изображений
- Веб-интерфейс с таблицей пользователей и пагинацией
- Модальное окно для создания пользователя с загрузкой нескольких изображений
- AJAX-подгрузка данных и создание пользователя

---

## Установка

1. Клонировать репозиторий:

```bash
git clone https://github.com/yourusername/laravel-users-images.git
cd laravel-users-images