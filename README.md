# 🍄 Logik – Mastermind

Logická hra Mastermind v prohlížeči. Hádej tajný kód podle zpětné vazby.

## Funkce

- **1 hráč** – hra proti počítači (3 obtížnosti)
- **2 hráči** – volba, kdo zadává kód a kdo hádl; po skončení hry se role střídají
- **Výběr políčka** – nejdřív vyber políčko, pak barvu
- **Zvukové efekty**
- **Tmavý / světlý režim**
- **Statistiky her**

## Spuštění

Otevři `index.html` v prohlížeči.

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
