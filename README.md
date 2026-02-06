# 🍄 Logik – Mastermind

Logická hra Mastermind v prohlížeči. Hádej tajný kód podle zpětné vazby.

## Funkce

- **1 hráč** – hra proti počítači (3 obtížnosti)
- **2 hráči** – volba, kdo zadává kód a kdo hádl; po skončení hry se role střídají
- **Online 2 hráči** – vytvoř hru, sdílej odkaz, druhý hráč se připojí odkudkoliv
- **Statistiky a online hra** – backend přes **PHP + MySQL** nebo **Supabase** (PostgreSQL v cloudu)
- **Výběr políčka** – nejdřív vyber políčko, pak barvu
- **Zvukové efekty**
- **Tmavý / světlý režim**
- **Statistiky her**

## Spuštění

Otevři `index.html` v prohlížeči. Pro **online hru a ukládání statistik** potřebuješ backend: buď **PHP + MySQL**, nebo **Supabase** (stačí frontend, žádný vlastní server).

### Možnost A: Supabase (doporučeno pro jednoduché nasazení)

1. Vytvoř projekt na [supabase.com](https://supabase.com) (zdarma).
2. V **SQL Editor** spusť skript **`migrations/supabase_001_tables.sql`** – vytvoří tabulky `results` a `games` včetně RLS.
3. V projektu: **Settings → API** zkopíruj **Project URL** a **publishable** (anon) klíč.
4. V repozitáři zkopíruj **`config.example.js`** na **`config.js`** a vyplň:
   ```javascript
   window.SUPABASE_URL = 'https://TVŮJ_PROJEKT.supabase.co';
   window.SUPABASE_ANON_KEY = 'tvůj_publishable_klíč';
   ```
   **`config.js`** může být v gitu – obsahuje jen publishable klíč (ten je určen pro prohlížeč). **Secret key** sem nikdy nedávej.
5. Aplikaci můžeš hostovat **kdekoliv** (GitHub Pages, Netlify, vlastní FTP) – stačí nahrát soubory. API běží na Supabase, nepotřebuješ PHP ani vlastní DB.

### Možnost B: PHP + MySQL

1. Vytvoř databázi a spusť migraci:
   ```bash
   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS logik_mastermind;"
   mysql -u root -p logik_mastermind < migrations/001_tables.sql
   ```
2. Konfigurace: v `api/config.php` (nebo proměnné prostředí) nastav přístup k DB:
   - `MYSQL_HOST`, `MYSQL_DBNAME`, `MYSQL_USER`, `MYSQL_PASSWORD`
3. Aplikaci servíruj přes PHP (např. `php -S localhost:8000` nebo Apache s PHP), aby volání na `api/*.php` fungovala.
4. V prohlížeči otevři `http://localhost:8000` (ne přímo soubor), aby API bylo na stejné doméně.

Pokud existuje **`config.js`** s vyplněnými `SUPABASE_URL` a `SUPABASE_ANON_KEY` (publishable klíč), aplikace použije Supabase. **Secret key** patří jen na backend (PHP/Node, .env na serveru), nikdy do frontendu ani do tohoto repa.

## Deploy na GitHub + FTP

### 1. Vytvoř repozitář na GitHubu

1. Jdi na [github.com/new](https://github.com/new)
2. Vytvoř nový repozitář (např. `logik-mastermind`)
3. **Nevyplňuj** README ani .gitignore (už existují)

### 2. Nastav GitHub Secrets pro FTP

V repozitáři: **Settings → Secrets and variables → Actions → New repository secret**

Přidej tyto secrets:

| Secret | Popis |
|--------|-------|
| `FTP_SERVER` | Adresa FTP serveru (např. `ftp.example.com`) |
| `FTP_USERNAME` | FTP uživatelské jméno |
| `FTP_PASSWORD` | FTP heslo |
| `FTP_SERVER_DIR` | *(volitelné)* Cesta na serveru (např. `/public_html` nebo `/www`). Pokud nevyplníš, použije se `/` |

### 3. Push na GitHub

```bash
cd c:\weby\logik-mastermind
git remote add origin https://github.com/TVUJ_USERNAME/logik-mastermind.git
git branch -M main
git push -u origin main
```

*(Nahraď `TVUJ_USERNAME` svým GitHub uživatelským jménem.)*

Po každém pushu do větve `main` nebo `master` se projekt automaticky nasadí na FTP.

---

## Co udělat pro nasazení (krok za krokem)

Aplikace potřebuje **webový server s PHP** a **MySQL**. FTP deploy nahraje jen soubory – na serveru musí být vše připravené.

### 1. Server

- **PHP** (např. 7.4+) s rozšířením `pdo_mysql`
- **MySQL** (nebo MariaDB)
- **HTTPS** doporučeno (kvůli odesílání dat na API)

### 2. Databáze na serveru

Vytvoř databázi a tabulky (např. přes phpMyAdmin nebo SSH):

```sql
CREATE DATABASE IF NOT EXISTS logik_mastermind CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Pak naimportuj soubor **`migrations/001_tables.sql`** (v phpMyAdmin: Import, nebo z příkazové řádky: `mysql -u UZIVATEL -p logik_mastermind < migrations/001_tables.sql`).

### 3. Konfigurace API

Na serveru uprav **`api/config.php`** (nebo vytvoř z `api/config.example.php`):

- `host` – adresa MySQL (často `localhost`)
- `dbname` – `logik_mastermind`
- `user` a `password` – přístup k databázi

Soubor `config.php` v gitu není (je v `.gitignore`), takže ho musíš na serveru vytvořit ručně nebo ho workflow doplní (např. z GitHub Secrets).

### 4. FTP deploy (GitHub Actions)

V repozitáři: **Settings → Secrets and variables → Actions** a nastav:

| Secret            | Význam        |
|-------------------|---------------|
| `FTP_SERVER`      | adresa FTP    |
| `FTP_USERNAME`     | FTP uživatel  |
| `FTP_PASSWORD`    | FTP heslo     |
| `FTP_SERVER_DIR`  | složka na serveru, např. `/public_html/logik` |

Po **pushu na `master`** (nebo `main`) se obsah repozitáře nahraje na FTP. Po prvním deployi **doplň na serveru `api/config.php`** (krok 3), pokud tam ještě není.

### 5. Ověření

- Otevři aplikaci v prohlížeči přes **https://tva-domena.cz/cesta/** (ne jako soubor z disku).
- Zkus **Online → Vytvořit hru** a **Statistiky** – pokud se načtou nebo uloží data, API a DB fungují.

**Shrnutí:** Na serveru musí běžet PHP + MySQL, v DB musí být tabulky z migrace, v `api/config.php` správné údaje k DB. FTP deploy pouze nahraje soubory z gitu.
