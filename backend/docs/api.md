# API Documentation

Base URL: `http://127.0.0.1:8000`

Authentication: Laravel Sanctum personal access tokens.

Login flow:
1. `POST /api/register` → 201
2. `POST /api/login` → copy `token`
3. Add header to protected routes: `Authorization: Bearer <token>`
4. `POST /api/logout` to revoke

Admin login:
`POST /api/admin/login` with `{ "username": "admin", "password": "password" }` → returns a Bearer token usable on admin-protected endpoints.

Note: Login endpoints are POST-only. A GET request to `/api/login` or `/api/admin/login` returns 405.

## Auth

### Register buyer
POST `/api/register`

Request
```json
{ "name": "Jane Buyer", "email": "jane@example.com", "password": "Password@123" }
```

201
```json
{ "response_code": 201, "status": "success", "message": "Successfully registered" }
```

### Login
POST `/api/login`

Request
```json
{ "email": "jane@example.com", "password": "Password@123" }
```

200
```json
{
  "status": "success",
  "token": "1|xxxxxxxx",
  "token_type": "Bearer",
  "user_info": { "id": 1, "name": "Jane Buyer", "email": "jane@example.com" }
}
```

### Logout
POST `/api/logout` (Bearer token required)

---

## Buyers (auth required)

- `GET /api/buyers` — Admin only
- `GET /api/buyers/{buyer}` — Admin or the same buyer
- `POST /api/buyers` — Admin only
- `PUT/PATCH /api/buyers/{buyer}` — Admin only
- `DELETE /api/buyers/{buyer}` — Admin only
- `GET /api/buyers/active` — token required

Example create
```json
{ "buyer_name": "Jane Buyer", "buyer_number": "B-1001", "email": "jane+profile@example.com", "address": "123 Main St" }
```

## Categories (auth required)
`GET /api/categories`, `POST /api/categories`, `GET /api/categories/{id}`, `PUT/PATCH /api/categories/{id}`, `DELETE /api/categories/{id}`

Create
```json
{ "category_name": "Graphics Cards" }
```

## Suppliers (auth required)
Create
```json
{ "supplier_name":"Acme Parts","contact_person":"Alice","phone_number":"09991234567","email":"sales@acme.test" }
```

## Hardware Components (auth required)

Create
```json
{
  "component_reference_number":"GPU-0001",
  "component_name":"RTX 4060",
  "brand":"NVIDIA",
  "model":"4060 Twin",
  "specifications":"8GB GDDR6",
  "retail_price":299.99,
  "seller_price":250.00,
  "category_id":1,
  "supplier_id":1,
  "date_created":"2025-10-27",
  "date_order":"2025-10-27",
  "date_arrive":"2025-10-28"
}
```

Analytics
- `GET /api/hardware-components/ordered/{start}/{end}`

## Orders (auth required)

- `GET /api/orders` — token required
- `GET /api/orders/{order}` — Admin or buyer who owns the order
- `POST /api/orders` — Admin only
- `PUT/PATCH/DELETE /api/orders/{order}` — Admin only
- `GET /api/orders/buyer/{buyer}` — token required
- `GET /api/orders/statistics` — token required

Create
```json
{
  "order_date":"2025-10-27",
  "total_amount":500.00,
  "shipping_status":"pending",
  "tracking_number":null,
  "estimated_delivery":"2025-10-30",
  "order_reference_number":"ORD-10001",
  "buyer_id":1,
  "admin_id":1,
  "payment_method":"cash",
  "selected_components":[{"component_id":1,"quantity":2}]
}
```

## Selected Components (auth required)
- `GET /api/selected-components` — token required
- `GET /api/orders/{order}/items` — token required
- `POST /api/orders/{order}/items` — token required

---

HTTP codes: 200, 201, 401, 403, 404, 422, 500. All responses are JSON.


//Register(Buyer)
curl -X POST "http://127.0.0.1:8000/api/register" ^
  -H "Content-Type: application/json" ^
  -d "{\"name\":\"Jurls\",\"email\":\"jurls@gmail.com\",\"password\":\"jurls123456\"}"

//Login
  curl -X POST "http://127.0.0.1:8000/api/login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"jurls@gmail.com\",\"password\":\"jurls123456\"}"

  Token
  curl -X GET "http://127.0.0.1:8000/api/buyers" ^
  -H "Authorization: Bearer YOUR_TOKEN" ^
  -H "Accept: application/json"

//Login as Admin
curl -X POST "http://127.0.0.1:8000/api/admin/login" -H "Content-Type: application/json" -d "{\"username\":\"admin\",\"password\":\"password\"}"

//Buyers List
curl -X GET "http://127.0.0.1:8000/api/get-user" -H "Authorization: Bearer YOUR_TOKEN"

//Logout
curl -X POST "http://127.0.0.1:8000/api/logout" -H "Authorization: Bearer YOUR_TOKEN"

//Healt
curl -X GET "http://127.0.0.1:8000/api"
curl -X GET "http://127.0.0.1:8000/api/ping"

//Admins only - Index
curl -X GET "http://127.0.0.1:8000/api/admins" -H "Authorization: Bearer ADMIN_TOKEN"

//Store
curl -X POST "http://127.0.0.1:8000/api/admins" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d "{\"username\":\"newadmin\",\"password\":\"Password@123\",\"role\":\"inventory_admin\"}"

//Show
curl -X GET "http://127.0.0.1:8000/api/admins/1" -H "Authorization: Bearer ADMIN_TOKEN"

//Update
curl -X PATCH "http://127.0.0.1:8000/api/admins/1" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d "{\"role\":\"manager\"}"

//Delete
curl -X DELETE "http://127.0.0.1:8000/api/admins/1" -H "Authorization: Bearer ADMIN_TOKEN"

//System/latest/count
curl -X GET "http://127.0.0.1:8000/api/admins/system" -H "Authorization: Bearer ADMIN_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/admins/latest" -H "Authorization: Bearer ADMIN_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/admins/count"  -H "Authorization: Bearer ADMIN_TOKEN"



//Buyers (token required; writes admin-only) - Index
curl -X GET "http://127.0.0.1:8000/api/buyers" -H "Authorization: Bearer YOUR_TOKEN"

//Store(Admin-only)
curl -X POST "http://127.0.0.1:8000/api/buyers" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d "{\"buyer_name\":\"Sample Customer\",\"buyer_number\":\"B-0001\",\"email\":\"customer@example.com\",\"address\":\"123 Main St\"}"

//Show
curl -X GET "http://127.0.0.1:8000/api/buyers/1" -H "Authorization: Bearer YOUR_TOKEN"

//Update
curl -X PATCH "http://127.0.0.1:8000/api/buyers/1" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d "{\"buyer_name\":\"Updated Name\",\"buyer_number\":\"B-0001\",\"email\":\"customer@example.com\",\"address\":\"456 New Ave\"}"

//Delete
curl -X DELETE "http://127.0.0.1:8000/api/buyers/1" -H "Authorization: Bearer ADMIN_TOKEN"

//Active/active-only/count/latest/buyer orders
curl -X GET "http://127.0.0.1:8000/api/buyers/active" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/buyers/active-only" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/buyers/count" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/buyers/latest" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/buyers/1/orders" -H "Authorization: Bearer YOUR_TOKEN"

//Categories(token required) - Index/Store/Show/Update/Delete
curl -X GET  "http://127.0.0.1:8000/api/categories" -H "Authorization: Bearer YOUR_TOKEN"
curl -X POST "http://127.0.0.1:8000/api/categories" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d "{\"category_name\":\"Graphics Cards\"}"
curl -X GET  "http://127.0.0.1:8000/api/categories/1" -H "Authorization: Bearer YOUR_TOKEN"
curl -X PATCH "http://127.0.0.1:8000/api/categories/1" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d "{\"category_name\":\"GPUs\"}"
curl -X DELETE "http://127.0.0.1:8000/api/categories/1" -H "Authorization: Bearer YOUR_TOKEN"

//With-components/empty
curl -X GET "http://127.0.0.1:8000/api/categories/with-components" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/categories/empty" -H "Authorization: Bearer YOUR_TOKEN"


//Suppliers (token required) - Index/Store/Show/Update/Delete
curl -X GET  "http://127.0.0.1:8000/api/suppliers" -H "Authorization: Bearer YOUR_TOKEN"
curl -X POST "http://127.0.0.1:8000/api/suppliers" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d "{\"supplier_name\":\"Acme Parts Co\",\"contact_person\":\"Alice Smith\",\"phone_number\":\"09991234567\",\"email\":\"sales@acme.test\"}"
curl -X GET  "http://127.0.0.1:8000/api/suppliers/1" -H "Authorization: Bearer YOUR_TOKEN"
curl -X PATCH "http://127.0.0.1:8000/api/suppliers/1" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d "{\"contact_person\":\"Bob\"}"
curl -X DELETE "http://127.0.0.1:8000/api/suppliers/1" -H "Authorization: Bearer YOUR_TOKEN"

//With-components/inactive
curl -X GET "http://127.0.0.1:8000/api/suppliers/with-components" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/suppliers/inactive" -H "Authorization: Bearer YOUR_TOKEN"


//Hardware Components (token required) - Index/Store/Show/Update/Delete
curl -X GET  "http://127.0.0.1:8000/api/hardware-components" -H "Authorization: Bearer YOUR_TOKEN"
curl -X POST "http://127.0.0.1:8000/api/hardware-components" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d "{\"component_reference_number\":\"GPU-0001\",\"component_name\":\"RTX 4060\",\"brand\":\"NVIDIA\",\"model\":\"Twin\",\"specifications\":\"8GB GDDR6, PCIe 4.0\",\"retail_price\":299.99,\"seller_price\":250.00,\"category_id\":1,\"supplier_id\":1,\"date_created\":\"2025-10-27\",\"date_order\":\"2025-10-27\",\"date_arrive\":\"2025-10-28\"}"
curl -X GET  "http://127.0.0.1:8000/api/hardware-components/1" -H "Authorization: Bearer YOUR_TOKEN"
curl -X PATCH "http://127.0.0.1:8000/api/hardware-components/1" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d "{\"retail_price\":289.99}"
curl -X DELETE "http://127.0.0.1:8000/api/hardware-components/1" -H "Authorization: Bearer YOUR_TOKEN"

//By category/supplier, low-stock, ordered range
curl -X GET "http://127.0.0.1:8000/api/hardware-components/category/1" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/hardware-components/supplier/1" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/hardware-components/low-stock"  -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/hardware-components/ordered/2025-10-01/2025-10-31" -H "Authorization: Bearer YOUR_TOKEN"


//Orders (token required; writes admin-only) - Index/Store/Show/Update/Delete
curl -X GET  "http://127.0.0.1:8000/api/orders" -H "Authorization: Bearer YOUR_TOKEN"
curl -X POST "http://127.0.0.1:8000/api/orders" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d "{\"order_date\":\"2025-10-27\",\"total_amount\":500.00,\"shipping_status\":\"pending\",\"tracking_number\":null,\"estimated_delivery\":\"2025-10-30\",\"order_reference_number\":\"ORD-10001\",\"buyer_id\":1,\"admin_id\":1,\"payment_method\":\"cash\",\"selected_components\":[{\"component_id\":1,\"quantity\":2}]}"
curl -X GET  "http://127.0.0.1:8000/api/orders/1" -H "Authorization: Bearer YOUR_TOKEN"
curl -X PATCH "http://127.0.0.1:8000/api/orders/1" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d "{\"shipping_status\":\"shipped\"}"
curl -X DELETE "http://127.0.0.1:8000/api/orders/1" -H "Authorization: Bearer ADMIN_TOKEN"

//Buyer/status/date/statistics
curl -X GET "http://127.0.0.1:8000/api/orders/buyer/1"              -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/orders/status/pending"       -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/orders/date/2025-10-01/2025-10-31" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/orders/statistics"           -H "Authorization: Bearer YOUR_TOKEN"


//Selected Components (token required) - Index
curl -X GET "http://127.0.0.1:8000/api/selected-components" -H "Authorization: Bearer YOUR_TOKEN"

//Order items list/add/update/delete
curl -X GET    "http://127.0.0.1:8000/api/orders/1/items" -H "Authorization: Bearer YOUR_TOKEN"
curl -X POST   "http://127.0.0.1:8000/api/orders/1/items" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d "{\"component_id\":1,\"quantity\":1}"
curl -X PATCH  "http://127.0.0.1:8000/api/orders/1/items/1" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d "{\"quantity\":3}"
curl -X DELETE "http://127.0.0.1:8000/api/orders/1/items/1" -H "Authorization: Bearer YOUR_TOKEN"

//Analytics
curl -X GET "http://127.0.0.1:8000/api/selected-components/order/1"     -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/selected-components/component/1" -H "Authorization: Bearer YOUR_TOKEN"
curl -X GET "http://127.0.0.1:8000/api/selected-components/most-ordered" -H "Authorization: Bearer YOUR_TOKEN"
