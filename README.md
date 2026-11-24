# Лабораторияи 11 — JavaScript: интерактивият

Мавзӯъ: **Баҳисобгирии вақти кории кормандон**
Фан: Технологияҳо ва забонҳои Internet барномасозӣ · ДПДТТХ

## Чӣ амалӣ шудааст

| Қисм | Тавсиф |
|---|---|
| Валидатсияи шакл | HTML + Constraint Validation API + `setCustomValidity` (санаи анҷом ≥ санаи оғоз), фокус ба аввалин майдони нодуруст |
| Ҷустуҷӯи зинда | `debounce` 300 мс, `fetch`, `response.ok`, `encodeURIComponent`, `AbortController` |
| Ҳолатҳо | Loading (`aria-busy`), Empty, Error; `aria-live="polite"` |
| Ҳимоя аз XSS | Додаҳо танҳо тавассути `textContent` |
| Вогузорӣ | Як коркардкунандаи `click` барои тугмаҳои «Нест кардан» + `<dialog>` барои тасдиқ |
| Беҳбудии прогрессивӣ | Бе JS ҷустуҷӯ — GET-и оддӣ ба `/records?q=` |

> Валидатсияи муштарӣ — қулайӣ аст, на ҳимоя: сервер ва пойгоҳ (CHECK) бояд аз нав санҷанд.

## Оғоз

```bash
node server.mjs
# http://localhost:3000
```

Ҷустуҷӯро бо «Раҳ» санҷед; сатри «Тестов» нишон медиҳад, ки `<img onerror>` иҷро намешавад.

## Сохтор

```
public/index.html   public/style.css   public/app.js
api/records.json    server.mjs (stub-и API)
```

Теги супориш: `day-11`
