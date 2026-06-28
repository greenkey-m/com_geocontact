# com_geocontact — Joomla 6

## Статус

**Для production на Joomla 6 — адаптирован.** Site и admin работают без fatal error и deprecation на PHP 8.4.

---

## Что готово

| Область | Статус |
|---|---|
| Архитектура J4+/6 | Namespace, `services/provider.php`, MVC, RouterView |
| Site (фронт) | SEF, детальная/список, морфология (Morphos), карта |
| Admin (бэкенд) | Searchtools, Atum, toolbar, сохранение записей |
| PHP 8.3/8.4 | Nullable-типы, `getIdentity()`, `getDatabase()`, без `JHtmlSidebar`/`getErrorMsg()` |
| Manifest | Версия **6.0.0**, `minimumPhp` 8.3, `minimumJoomla` 6.0 |
| SQL | Install utf8mb4 + миграция `6.0.0.sql` |
| Composer | `wapmorgan/morphos`, vendor в `.gitignore` |

---

## Что работает, но не идеально (не блокирует J6)

- [ ] **Экспорт XML** — кнопка есть, функция не реализована (сообщение в админке)
- [ ] **Import из `towns.xml`** — работает локально из `administrator/components/com_geocontact/towns.xml` (dev/symlink); в ZIP-пакете файла нет (намеренно)
- [ ] **Шаблон detail (site)** — старые классы Bootstrap 3 (`col-sm-6`), inline JS карты — работает, но не в стиле BS5 шаблона
- [ ] **`Factory::getUser($id)`** в `CreatedbyField` — допустимо для загрузки пользователя по ID; deprecated только вызов без аргумента
- [ ] **`Categories::getInstance()`** в import — ещё есть в J6, но со временем лучше перейти на `CategoryFactory`
- [ ] **`GeocontactTable::publish()`** — использует `$this->_db`; для класса `Table` это нормально

---

## Деплой

```bash
cd C:\Users\info\PhpstormProjects\com_geocontact
composer install
```

Затем обновить компонент в Joomla до **6.0.0** (применится SQL-миграция utf8mb4).

---

## Следующие шаги (опционально)

1. Реализовать export XML или убрать кнопку из toolbar
2. Обновить site-шаблон detail под Bootstrap 5
3. Заменить `Categories::getInstance` на `CategoryFactory`
4. Восстановить полный набор колонок в admin-списке (если нужно)
5. Прогнать полный цикл: install/update 6.0.0 на чистой J6, import towns, проверка ACL

---

## Ключевые файлы

```
com_geocontact/
├── geocontact.xml                    # дистрибутив 6.0.0 + vendor
├── administrator/geocontact.xml      # dev symlink, без vendor
├── administrator/src/Table/GeocontactTable.php
├── administrator/src/View/Geocontacts/HtmlView.php
├── administrator/tmpl/geocontacts/default.php
├── site/src/Service/Router.php
├── site/src/Service/Rules/AliasParseRules.php
├── site/src/Helper/DescriptionHelper.php
└── administrator/sql/updates/mysql/6.0.0.sql
```
