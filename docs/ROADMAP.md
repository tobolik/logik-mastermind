# Návrh dalších funkcí (roadmap)

Návrhy na vylepšení po konzultaci s uživatelem. Není zatím implementováno.

---

## 1. Registrace a přihlášení

- **Supabase Auth** (e-mail + heslo nebo magic link).
- Po přihlášení: uložit `user.id` a zobrazit přihlášeného hráče (jméno z profilu).
- Všechny výsledky her ukládat s `user_id` (nebo jen `player_name` odvozeným z účtu), aby byla historie vázaná na účet.

---

## 2. Hráčská karta (profil)

- Stránka / záložka **Můj profil** nebo **Hráčská karta**.
- Zobrazení: jméno, avatar (volitelně), souhrn statistik (celkem her, výhry, nejlepší počet pokusů, průměr), historie posledních her.
- Možnost upravit zobrazované jméno (v rámci účtu).

---

## 3. Kamarádi a vyhledávání

- Tabulka **friends** (např. `user_id`, `friend_id`, stav: čeká / přijato).
- UI: **Přidat kamaráda** – vyhledání podle jména nebo e-mailu (pokud je veřejný), nebo sdílení odkazu na profil.
- Seznam **Moji kamarádi** s možností pozvat ke hře (viz níže).

---

## 4. Pozvánka ke hře a notifikace

- **Pozvat kamaráda:** z výběru kamaráda vytvořit online hru a poslat mu pozvánku (záznam v tabulce `invites`: kdo, koho, game_code, vytvořeno).
- **Notifikace:**  
  - **Push notifikace** (prohlížeč): Service Worker + Push API, vyžaduje souhlas uživatele; při příchozí pozvánce zobrazit „Pozvánka od X – připojit se?“  
  - **Alternativa:** při otevření aplikace zkontrolovat nevyřízené pozvánky (Supabase realtime nebo dotaz na `invites`) a zobrazit banner / modální okno „Máš pozvánku od X – Připojit se“.
- Po přijetí pozvánky: přesměrovat na hru (odkaz s `?online=join&code=...`).

---

## Pořadí implementace (návrh)

1. **Registrace + přihlášení** (Supabase Auth) a propojení výsledků s uživatelem.
2. **Hráčská karta** – zobrazení statistik a historie pro přihlášeného uživatele.
3. **Pozvánky** – vytvoření hry + uložení pozvánky, kontrola při načtení stránky a zobrazení výzvy „Připojit se“ (bez push).
4. **Kamarádi** – přidání kamaráda, seznam, výběr při pozvání.
5. **Push notifikace** (volitelně) – Service Worker, Push API, zobrazení notifikace i když aplikace není otevřená.

---

*Tento soubor slouží jako zápis požadavků a návrh dalšího směru vývoje. Konkrétní implementace závisí na prioritách.*
