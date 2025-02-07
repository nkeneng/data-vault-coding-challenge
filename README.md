# Data Privacy Vault Coding Challenge Zasta GmbH

## Table of Contents

- [Features](#features)
- [Architecture Overview](#architecture-overview)
- [Installation](#installation)
- [API Endpoints](#api-endpoints)
- [Authentication](#authentication)

## Features

- **Tokenization & Detokenization:**  
  Replace sensitive field values with tokens and later retrieve the original values.
  
- **Encryption:**  
  Uses AES-256-GCM to encrypt sensitive data before persisting it.
  
- **Persistent Storage:**  
  Leverages Doctrine ORM to store token–encrypted value pairs in an sqlit database.
  
- **Service-to-Service Authentication:**  
  Custom API key authenticator secures endpoints using hashed API keys.
  
- **Robust Error Handling and Logging:**  
  All critical operations are wrapped in transactions and logged with Monolog.
  
- **Configuration via Environment Variables:**  
  All sensitive configuration (encryption keys, API keys, etc.) is managed through environment variables.

## Architecture Overview

The project is built with a clear separation of concerns:

- **Controllers:**  
  Handle HTTP requests, validate input, and return appropriate responses.
  
- **Services:**  
  - **TokenizationService:** Manages token generation, encryption/decryption, and persistence.  
  - **EncryptionService:** Encapsulates AES-256-GCM encryption and decryption logic.
  
- **Security:**  
  - **ApiTokenAuthenticator:** A custom authenticator that secures the endpoints using API keys.
  
- **Persistence:**  
  - **SensitiveData Entity:** Represents token–encrypted value pairs stored in the database.

This modular design ensures that each component is testable, maintainable, and scalable.

## Installation

### Prerequisites

- PHP 8.0 or higher
- Composer
- An Sqlite database
- [Symfony CLI](https://symfony.com/download) (optional but recommended)

### Steps

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/nkeneng/data-privacy-vault.git
   cd data-privacy-vault
   ```

2. **Install Dependencies:**

```bash
composer install
```

3. **Database Setup:**
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## API Endpoints

**Tokenize**
* endpoint: /tokenize
* Method: POST
* Description: Accepts sensitive data, tokenizes each field by generating an 8-character alphanumeric token, encrypts the original value, and persists it.
* Request Example:
```json
{
  "id": "req-123",
  "data": {
    "field1": "value1",
    "field2": "value2",
    "fieldn": "valuen"
  }
}
```

* Response example:
```json
{
  "id": "req-123",
  "data": {
    "field1": "t8yk4f5",
    "field2": "gj45nkd",
    "fieldn": "bkj6iob"
  }
}
```
**Detokenize**
* endpoint: /detokenize
* Method: POST
* Description: Accepts tokens, retrieves the corresponding encrypted data from the database, decrypts it, and returns the original values.
* Request Example:
```json
{
  "id": "req-33445",
  "data": {
    "field1": "t8yk4f5",
    "field2": "gj45nkd",
    "field3": "invalid token"
  }
}
```
* Response Example
```json
{
  "id": "req-33445",
  "data": {
    "field1": {
      "found": true,
      "value": "value1"
    },
    "field2": {
      "found": true,
      "value": "value2"
    },
    "field3": {
      "found": false,
      "value": ""
    }
  }
}
```
## Authentication
All requests to the API endpoints must include an X-AUTH-TOKEN HTTP header with one of the allowed API keys. The API key is hashed and compared against pre-computed hashes to authenticate service-to-service calls.
* Example Header
```http
X-API-KEY: supersecretapikey1
```
If authentication fails, the API returns a 401 Unauthorized error.

