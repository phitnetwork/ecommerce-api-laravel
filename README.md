# Ecommerce API – Filament 4 + Laravel 12

API REST per un e-commerce di prodotti **digitali** (catalogo, filtri, paginazione, CRUD, test).

ℹ️ **About**  
Questo repository nasce come esercizio tecnico per mostrare approccio, struttura e qualità del codice in Laravel 12 con Filament 4. 
Non è destinato all’uso in produzione, ma a scopo dimostrativo e valutativo delle competenze dell’autore.

---

## ✅ Requisiti

- PHP 8.2+
- Composer
- Estensioni: `pdo`, `mbstring`, `openssl`
- DB: MySQL/MariaDB o SQLite

---

## 🚀 Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

**(Opzionale)** imposta nel `.env` le credenziali dell'utente seed:
```
SEED_ADMIN_EMAIL=tuaemail@dominio.com
SEED_ADMIN_PASSWORD=password
```

---

## 🐳 Docker / Docker Compose (opzionale)

**Opzione A – Laravel Sail**

```bash
composer require laravel/sail --dev
php artisan sail:install --with=mysql
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan test
```

**Opzione B – Docker Compose classico** (se nel repo c'è un `docker-compose.yml` con un servizio PHP chiamato `app`):

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan test
```

App disponibile su `http://localhost:8000` (o sulla porta esposta dal tuo compose).

---

## 🧪 Test

Crea un file `.env.testing` con:

```
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

Esegui i test:

```bash
php artisan test
```

---

## 📦 Entità

- **Product**: `name`, `description`, `image`, `category_id (nullable)`, relazione molti‑a‑molti con **Tag**
- **Category**: `name`, `slug`
- **Tag**: `name`, `slug`

---

## 🎛 Admin (Filament 4)

- Pannello amministrativo per gestione CRUD di **Product**, **Category**, **Tag**.
- Accesso: **/admin**
- **Credenziali (utente seed)**: lette da `.env` → `SEED_ADMIN_EMAIL` / `SEED_ADMIN_PASSWORD`  
  - default (se non impostati): `admin@example.com` / `password`
- Le API sono indipendenti: Filament è solo un comodo backend.
- Se non serve, puoi omettere il pannello admin e non esporre `/admin`.

## 🔐 Autenticazione

- **API REST**: aperte, nessuna autenticazione applicata (scopo esercitazione/valutazione). Nessun middleware `auth` sulle route `/api/*`.
- **Admin Filament**: protetto da login; usa l’utente seed indicato sopra.
- (Produzione) Opzionale: protezione con **Sanctum/Passport**, rate‑limit, CORS mirato.

## 📡 Endpoints

### Products

- `GET /api/products` → lista con **paginazione** e **filtri**
  - `q` (string) → ricerca su `name`
  - `category` (id **o** slug)
  - `tag` (id **o** slug)
  - `per_page` (int, default 12, max 100)
- `POST /api/products` → crea
- `GET /api/products/{id}` → dettaglio
- `PATCH /api/products/{id}` → aggiorna
- `DELETE /api/products/{id}` → elimina

**Esempio body (create)**

```json
{
  "category_id": 1,
  "name": "Laravel 12 Quickstart",
  "description": "Manuale pratico",
  "image": "https://cdn.example.com/img.png",
  "tags": ["laravel", "php", 3]
}
```

**Note utili**

- In input `tags` accetta array di **id**, **slug** o **nomi**; quelli non esistenti vengono creati al volo.
- In `PATCH`, `tags: null` ➜ lasci invariato; `tags: []` ➜ stacchi tutti i tag.
- Output JSON dei prodotti include `category` e `tags` con campi essenziali.

---

## 🔎 Paginazione & Filtri

- Risposta paginata con chiavi `data`, `links`, `meta` (stile Laravel).
- Filtri combinabili: `q + category + tag`.
- Esempio:
  ```
  GET /api/products?q=laravel&category=courses&tag=php&per_page=10
  ```

---

## 🧱 Scelte architetturali

- **FormRequest** per validazione (`StoreProductRequest`, `UpdateProductRequest`).
- **API Resource** per output pulito (`ProductResource`, `CategoryResource`, `TagResource`).
- **Scope** di filtro in `Product` per `q/category/tag`.
- **Transazioni** su create/update con gestione tag.
- **HTTP status** corretti: 201 (create), 204 (delete), 422 (validation).
- **DB di test**: SQLite in‑memory via `.env.testing`.

---

## 🧰 Esempi rapidi (curl)

```bash
# List (filtri + paginazione)
curl -s 'http://localhost:8000/api/products?q=laravel&category=courses&tag=php&per_page=10' | jq

# Create
curl -s -X POST 'http://localhost:8000/api/products' \
  -H 'Content-Type: application/json' \
  -d '{"category_id":1,"name":"Digital Pack","description":"Desc","image":"https://example.test/img.png","tags":["php","backend"]}' | jq

# Update (svuota tag)
curl -s -X PATCH 'http://localhost:8000/api/products/1' \
  -H 'Content-Type: application/json' \
  -d '{"name":"Digital Pack Pro","tags":[]}' | jq

# Delete
curl -i -X DELETE 'http://localhost:8000/api/products/1'
```

## 🤖 Nota sull'utilizzo di AI

Per lo sviluppo di questo progetto ho utilizzato un assistente virtuale (AI) come supporto nella generazione di boilerplate e documentazione. La progettazione, la struttura del codice e le decisioni architetturali restano comunque frutto di valutazioni personali.  

L'obiettivo è mostrare non solo competenze tecniche, ma anche la capacità di integrare in modo consapevole strumenti moderni a supporto dello sviluppo.