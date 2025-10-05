# Contact Management Backend API

A simple Laravel project to learn and implement RESTful API concepts.

## Features

- CRUD operations for resources
- Authentication
- API resource responses
- Validation and error handling

## Requirements

- PHP >= 8.0
- Composer
- Laravel >= 10.x
- MySQL or other supported database

## Installation

```bash
git clone https://github.com/yourusername/contact-management-api.git
cd contact-management-api
composer install
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file with database credentials.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

## Usage
```bash
php artisan migrate
php artisan serve
```

Access the API at `http://localhost:8000`.

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | /api/users | Register new user |
| POST   | /api/users/login | User login |
| GET    | /api/users/current | Get current user info (auth required) |
| PATCH  | /api/users/current | Update current user info (auth required) |
| DELETE | /api/users/logout | Logout user (auth required) |
| POST   | /api/contacts | Create contact (auth required) |
| GET    | /api/contacts | Search/list contacts (auth required) |
| GET    | /api/contacts/{id} | Get contact by ID (auth required) |
| PUT    | /api/contacts/{id} | Update contact by ID (auth required) |
| DELETE | /api/contacts/{id} | Delete contact by ID (auth required) |
| POST   | /api/contacts/{idContact}/addresses | Add address to contact (auth required) |
| GET    | /api/contacts/{idContact}/addresses | List addresses for contact (auth required) |
| GET    | /api/contacts/{idContact}/addresses/{idAddress} | Get address by ID for contact (auth required) |
| PUT    | /api/contacts/{idContact}/addresses/{idAddress} | Update address by ID for contact (auth required) |
| DELETE | /api/contacts/{idContact}/addresses/{idAddress} | Delete address by ID for contact (auth required) |

## API Specification

### User Registration

**Request**
```
POST /api/users
Content-Type: application/json
```
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "yourpassword"
}
```
**Response**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2024-06-01T12:00:00.000000Z"
}
```

### User Login

**Request**
```
POST /api/users/login
Content-Type: application/json
```
```json
{
    "email": "john@example.com",
    "password": "yourpassword"
}
```
**Response**
```json
{
    "token": "<auth_token>"
}
```

### Get Current User (Auth Required)

**Request**
```
GET /api/users/current
Authorization: <auth_token>
```
**Response**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
}
```

### Update Current User (Auth Required)

**Request**
```
PATCH /api/users/current
Authorization: <auth_token>
Content-Type: application/json
```
```json
{
    "name": "Jane Doe"
}
```
**Response**
```json
{
    "id": 1,
    "name": "Jane Doe",
    "email": "john@example.com"
}
```

### Logout (Auth Required)

**Request**
```
DELETE /api/users/logout
Authorization: <auth_token>
```
**Response**
```json
{
    "message": "Logged out successfully."
}
```

### Create Contact (Auth Required)

**Request**
```
POST /api/contacts
Authorization: <auth_token>
Content-Type: application/json
```
```json
{
    "name": "Alice",
    "phone": "1234567890",
    "email": "alice@example.com"
}
```
**Response**
```json
{
    "id": 1,
    "name": "Alice",
    "phone": "1234567890",
    "email": "alice@example.com",
    "created_at": "2024-06-01T12:00:00.000000Z"
}
```

### List/Search Contacts (Auth Required)

**Request**
```
GET /api/contacts
Authorization: <auth_token>
```
**Response**
```json
[
    {
        "id": 1,
        "name": "Alice",
        "phone": "1234567890",
        "email": "alice@example.com"
    }
]
```

### Get Contact by ID (Auth Required)

**Request**
```
GET /api/contacts/1
Authorization: <auth_token>
```
**Response**
```json
{
    "id": 1,
    "name": "Alice",
    "phone": "1234567890",
    "email": "alice@example.com"
}
```

### Update Contact by ID (Auth Required)

**Request**
```
PUT /api/contacts/1
Authorization: <auth_token>
Content-Type: application/json
```
```json
{
    "name": "Alice Updated",
    "phone": "0987654321"
}
```
**Response**
```json
{
    "id": 1,
    "name": "Alice Updated",
    "phone": "0987654321",
    "email": "alice@example.com"
}
```

### Delete Contact by ID (Auth Required)

**Request**
```
DELETE /api/contacts/1
Authorization: <auth_token>
```
**Response**
```json
{
    "message": "Contact deleted successfully."
}
```

### Add Address to Contact (Auth Required)

**Request**
```
POST /api/contacts/1/addresses
Authorization: <auth_token>
Content-Type: application/json
```
```json
{
    "street": "123 Main St",
    "city": "Metropolis",
    "postal_code": "12345"
}
```
**Response**
```json
{
    "id": 1,
    "street": "123 Main St",
    "city": "Metropolis",
    "postal_code": "12345",
    "contact_id": 1,
    "created_at": "2024-06-01T12:00:00.000000Z"
}
```

### List Addresses for Contact (Auth Required)

**Request**
```
GET /api/contacts/1/addresses
Authorization: <auth_token>
```
**Response**
```json
[
    {
        "id": 1,
        "street": "123 Main St",
        "city": "Metropolis",
        "postal_code": "12345",
        "contact_id": 1
    }
]
```

### Get Address by ID for Contact (Auth Required)

**Request**
```
GET /api/contacts/1/addresses/1
Authorization: <auth_token>
```
**Response**
```json
{
    "id": 1,
    "street": "123 Main St",
    "city": "Metropolis",
    "postal_code": "12345",
    "contact_id": 1
}
```

### Update Address by ID for Contact (Auth Required)

**Request**
```
PUT /api/contacts/1/addresses/1
Authorization: <auth_token>
Content-Type: application/json
```
```json
{
    "street": "456 Elm St",
    "city": "Gotham"
}
```
**Response**
```json
{
    "id": 1,
    "street": "456 Elm St",
    "city": "Gotham",
    "postal_code": "12345",
    "contact_id": 1
}
```

### Delete Address by ID for Contact (Auth Required)

**Request**
```
DELETE /api/contacts/1/addresses/1
Authorization: <auth_token>
```
**Response**
```json
{
    "message": "Address deleted successfully."
}
```

## License

This project is open-source and available under the [MIT License](LICENSE).