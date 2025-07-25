# 📄 API Invoices - Laravel

API RESTful para la gestión de facturas, desarrollada en **Laravel**, siguiendo principios **SOLID**, buenas prácticas y arquitectura limpia.

---

## 📚 Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Ejecución](#ejecución)
- [Pruebas](#pruebas)
- [Endpoints Principales](#endpoints-principales)
- [Autenticación](#autenticación)
- [Ejemplo de Peticiones](#ejemplo-de-peticiones)
- [Notas](#notas)

---

## ✅ Requisitos

- PHP >= 8.1  
- Composer  
- SQLite, MySQL o PostgreSQL  
- Laravel >= 10.x  

---

## 🚀 Instalación

```bash
git clone https://github.com/luisJaimeB/api-invoices.git
cd api-invoices
composer install
cp .env.example .env
php artisan key:generate
```

---

## ⚙️ Configuración

Edita tu archivo `.env` con los siguientes valores mínimos:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY= # Llave generada por artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# O configura MySQL/PostgreSQL según tu entorno

API_LOGIN=tu_usuario_api
API_SECRET=tu_clave_secreta
API_SITE_ID=tu_site_id
```

Luego configura las credenciales en `config/services.php`:

```php
'api_auth' => [
    'login' => env('API_LOGIN', 'usuario_api'),
    'secret' => env('API_SECRET', 'clave_secreta'),
],
```

Crea la base de datos y ejecuta migraciones:

```bash
touch database/database.sqlite
php artisan migrate
```

Pobla la base de datos con datos de prueba (opcional):

```bash
php artisan db:seed
```

---

## ▶️ Ejecución

```bash
php artisan serve
```

---

## 📌 Endpoints Principales

| Método | Endpoint                    | Descripción                |
|--------|-----------------------------|----------------------------|
| POST   | `/api/invoices`             | Crear factura              |
| POST   | `/api/invoices/search`      | Buscar factura             |
| POST   | `/api/invoice/hold`         | Bloquear/activar factura   |
| POST   | `/api/invoices/settle`      | Asentar (pagar) factura    |

---

## 🔐 Autenticación

Todos los endpoints requieren un objeto `auth` en el cuerpo del JSON:

```json
"auth": {
    "login":"personalizado_pruebas",
    "secretKey":"secret",
    "tranKey":"H/KXHl4AptMFZzTJyWNQWdSf1NOAr21Ln2Swg3BdGqA=","nonce":"NWF6bXY0OTlxbw==",
    "seed":"2025-07-25T16:26:11-05:00"
}
```
Pueden generarlo con el siguiente pre-request script para postman:

```json
var moment = require('moment')
var CryptoJS = require('crypto-js')

var auth = {
    'login': pm.collectionVariables.get('login'),
    'secretKey': pm.collectionVariables.get('secretKey')
}

var nonce = Math.random().toString(36).substring(2)
var seed = moment().format()
hash = CryptoJS.SHA256(nonce + seed + auth.secretKey)

auth.tranKey = hash.toString(CryptoJS.enc.Base64)

auth.nonce = btoa(nonce)
auth.seed = seed

console.log('nonce (plain):', nonce);
console.log('nonce (base64):', btoa(nonce));
console.log('seed:', seed);
console.log('secretKey:', auth.secretKey);
console.log('rawString:', nonce + seed + auth.secretKey);

pm.environment.set('auth', JSON.stringify(auth))

IdRondom = Math.floor(Math.random() * 1000000)

pm.environment.set('IdRondom', IdRondom.toString())
```

---

## 📤 Ejemplo de Peticiones

### 🧾 Crear Factura

```http
POST /api/invoices
Content-Type: application/json
Accept: application/json
```

```json
{
  "auth": { ... },
  "debtor_document": "104001234",
  "debtor_document_type": "CC",
  "debtor_name": "Juan",
  "debtor_surname": "Pérez",
  "debtor_email": "juan@example.com",
  "payment_reference": "REF12345",
  "payment_description": "Compra de producto",
  "payment_currency": "COP",
  "payment_total": 150000,
  "payment_allow_partial": false,
  "payment_subscribe": false,
  "alt_reference": null,
  "expiration_date": "2025-08-01T23:59:59-05:00"
}
```

---

### 🔍 Buscar Factura

```http
POST /api/invoices/search
Content-Type: application/json
Accept: application/json
```

```json
{
  "auth": { ... },
    "agreement": "0000",
    "searchType": "document",
    "searchValue": "123456789",
    "siteId": "tuSiteIdDePruebas"
}
```

---

### 🔒 HOLD (bloquear o activar factura)

```http
POST /api/invoice/hold
Content-Type: application/json
Accept: application/json
```

```json
{
  "auth": { ... },
  "id": 1,
  "reference": "REF12345",
  "revoke": false,
  "siteId": "tuSiteIdDePruebas"
}
```

> `revoke: false` para **bloquear**, `true` para **activar**.

---

### 💳 Asentar Factura

```http
POST /api/invoices/settle
Content-Type: application/json
Accept: application/json
```

```json
{
  "auth": { ... },
    "id": 1423,
    "reference": "NCTPTJJD",
    "agreement": "0000",
    "authorization": "123456",
    "receipt": "34567",
    "method": "pse",
    "franchise": "VISA",
    "franchiseName": "VISA",
    "issuerName": "BANCO DE PRUEBAS",
    "lastDigits": null,
    "provider": "provider",
    "internalReference": 34567,
    "amount": {
        "currency": "COP",
        "total": 19335.33
    },
    "date": "2025-07-25T15:49:37-05:00",
    "channel": "PRUEBAS",
    "paymentMethod": "VISA",
    "location": "PRUEBAS",
    "siteId": "tuSiteIdDePruebas",
    "requestId": 12345,
    "locale": "es_CO"
}
```

---

## 📝 Notas

- Todos los endpoints responden en formato JSON.
- Se recomienda usar HTTPS en entornos productivos.
- Las fechas deben seguir el formato ISO 8601: `Y-m-d\TH:i:sP`.
