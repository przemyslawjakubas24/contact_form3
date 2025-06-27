# Struktura po przeniesieniu config.php

## 📁 Nowa struktura katalogów:

```
serwer/
├── config.php                    ⚠️ PRZENIESIONY TUTAJ (bezpieczny)
└── public_html/
    ├── index.html
    ├── styles.css
    ├── script.js
    ├── send_email.php            ✅ zaktualizowany (require_once '../config.php')
    ├── test_smtp.php             ✅ zaktualizowany (require_once '../config.php')
    ├── composer.json
    ├── .htaccess                 ✅ usunięto ochronę config.php (już niepotrzebna)
    └── vendor/
```

## 🔧 Wprowadzone zmiany:

1. **send_email.php** - zmieniono ścieżkę: `require_once '../config.php'`
2. **test_smtp.php** - zmieniono ścieżkę: `require_once '../config.php'`
3. **.htaccess** - usunięto sekcję ochrony config.php (plik już poza public_html)

## ✅ Korzyści z przeniesienia:

- ✅ **Maksymalne bezpieczeństwo** - plik całkowicie niedostępny przez HTTP
- ✅ **Brak potrzeby .htaccess** - fizyczna separacja od katalogu publicznego
- ✅ **Lepsze praktyki** - standardowe podejście w aplikacjach PHP

## 🚀 Uprawnienia na serwerze:

```bash
# Dla pliku config.php poza public_html:
chmod 600 ../config.php

# Dla pozostałych plików w public_html:
chmod 644 *.php *.html *.css *.js
chmod 755 vendor/
```

Formularz powinien teraz działać poprawnie! 🎉
