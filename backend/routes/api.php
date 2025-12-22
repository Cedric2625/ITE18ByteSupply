<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HardwareComponentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SelectedComponentController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// (Reverted) No GET handlers for login endpoints; POST-only

Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
    // --------------- Register and Login ----------------//
    Route::post('register', 'AuthenticationController@register')->middleware('throttle:6,1')->name('api.register');
	// curl -X POST "http://127.0.0.1:8001/api/register" \
	//   -H "Content-Type: application/json" \
	//   -d '{"name":"Jane Buyer","email":"jane@example.com","password":"Password@123"}'

    Route::post('login', 'AuthenticationController@login')->middleware('throttle:10,1')->name('api.login');
	// curl -X POST "http://127.0.0.1:8001/api/login" \
	//   -H "Content-Type: application/json" \
	//   -d '{"email":"jane@example.com","password":"Password@123"}'

    Route::post('admin/login', 'AuthenticationController@adminLogin')->middleware('throttle:10,1')->name('api.admin.login');
	// curl -X POST "http://127.0.0.1:8001/api/admin/login" \
	//   -H "Content-Type: application/json" \
	//   -d '{"username":"admin","password":"password"}'
    
    // Password Reset API Routes
    Route::post('password/old', 'AuthenticationController@changeWithOldPassword')->middleware('throttle:5,1')->name('api.password.old');
    Route::post('password/otp/verify', 'AuthenticationController@verifyCode')->middleware('throttle:5,1')->name('api.password.otp.verify');
    Route::post('password/new', 'AuthenticationController@setNewPassword')->middleware('throttle:5,1')->name('api.password.new');
    
    // ------------------ Get Data ----------------------//
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('get-user', 'AuthenticationController@userInfo')->name('get-user');
		// curl -X GET "http://127.0.0.1:8001/api/get-user" \
		//   -H "Authorization: Bearer YOUR_TOKEN" \
		//   -H "Accept: application/json"

        Route::post('logout', 'AuthenticationController@logOut')->name('logout');
		// curl -X POST "http://127.0.0.1:8001/api/logout" \
		//   -H "Authorization: Bearer YOUR_TOKEN"
    });
});

// Health Check
Route::get('/', function () {
    return response()->json([
        'message' => (env('APP_NAME', 'System') . ' API is working'),
        'status' => 'success',
    ]);
});
// curl -X GET "http://127.0.0.1:8001/api"

Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!']);
});
// curl -X GET "http://127.0.0.1:8001/api/ping"

// Admin API Routes (protected)
Route::middleware(['auth:sanctum', 'can:admin-only'])->as('api.')->group(function () {
    Route::prefix('admins')->group(function () {
        Route::get('system', [AdminController::class, 'systemAdmins']);
		// curl -X GET "http://127.0.0.1:8001/api/admins/system" \
		//   -H "Authorization: Bearer ADMIN_TOKEN"

        Route::get('latest', [AdminController::class, 'latest']);
		// curl -X GET "http://127.0.0.1:8001/api/admins/latest" \
		//   -H "Authorization: Bearer ADMIN_TOKEN"

        Route::get('count', [AdminController::class, 'count']);
		// curl -X GET "http://127.0.0.1:8001/api/admins/count" \
		//   -H "Authorization: Bearer ADMIN_TOKEN"
    });
    Route::apiResource('admins', AdminController::class);
	// curl -X GET    "http://127.0.0.1:8001/api/admins"           -H "Authorization: Bearer ADMIN_TOKEN"
	// curl -X POST   "http://127.0.0.1:8001/api/admins"           -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"username":"newadmin","password":"Password@123","role":"inventory_admin"}'
	// curl -X GET    "http://127.0.0.1:8001/api/admins/1"         -H "Authorization: Bearer ADMIN_TOKEN"
	// curl -X PATCH  "http://127.0.0.1:8001/api/admins/1"         -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"role":"order_admin"}'
	// curl -X DELETE "http://127.0.0.1:8001/api/admins/1"         -H "Authorization: Bearer ADMIN_TOKEN"
});

// Buyer API Routes (protected)
Route::middleware(['auth:sanctum'])->as('api.')->group(function () {
    Route::prefix('buyers')->group(function () {
        Route::get('active', [BuyerController::class, 'withActiveOrders']);
		// curl -X GET "http://127.0.0.1:8001/api/buyers/active" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('active-only', [BuyerController::class, 'activeOnly']);
		// curl -X GET "http://127.0.0.1:8001/api/buyers/active-only" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('count', [BuyerController::class, 'count']);
		// curl -X GET "http://127.0.0.1:8001/api/buyers/count" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('latest', [BuyerController::class, 'latest']);
		// curl -X GET "http://127.0.0.1:8001/api/buyers/latest" -H "Authorization: Bearer YOUR_TOKEN"

        // Linking: /api/buyers/{buyer}/orders
        Route::get('{buyer}/orders', [OrderController::class, 'byBuyer']);
		// curl -X GET "http://127.0.0.1:8001/api/buyers/1/orders" -H "Authorization: Bearer YOUR_TOKEN"
    });
    Route::apiResource('buyers', BuyerController::class);
	// curl -X GET    "http://127.0.0.1:8001/api/buyers"           -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X POST   "http://127.0.0.1:8001/api/buyers"           -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"buyer_name":"Sample","buyer_number":"123456789","email":"sample@example.com","address":"123 Main St"}'
	// curl -X GET    "http://127.0.0.1:8001/api/buyers/1"         -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X PATCH  "http://127.0.0.1:8001/api/buyers/1"         -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"address":"456 New Ave"}'
	// curl -X DELETE "http://127.0.0.1:8001/api/buyers/1"         -H "Authorization: Bearer ADMIN_TOKEN"
});

// Category API Routes (protected)
Route::middleware(['auth:sanctum'])->as('api.')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('with-components', [CategoryController::class, 'withComponentsCount']);
		// curl -X GET "http://127.0.0.1:8001/api/categories/with-components" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('empty', [CategoryController::class, 'empty']);
		// curl -X GET "http://127.0.0.1:8001/api/categories/empty" -H "Authorization: Bearer YOUR_TOKEN"
    });
    Route::apiResource('categories', CategoryController::class);
	// curl -X GET    "http://127.0.0.1:8001/api/categories"       -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X POST   "http://127.0.0.1:8001/api/categories"       -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"category_name":"Graphics Cards"}'
	// curl -X GET    "http://127.0.0.1:8001/api/categories/1"     -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X PATCH  "http://127.0.0.1:8001/api/categories/1"     -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"category_name":"GPUs"}'
	// curl -X DELETE "http://127.0.0.1:8001/api/categories/1"     -H "Authorization: Bearer YOUR_TOKEN"
});

// Supplier API Routes (protected)
Route::middleware(['auth:sanctum'])->as('api.')->group(function () {
    Route::prefix('suppliers')->group(function () {
        Route::get('with-components', [SupplierController::class, 'withComponentsCount']);
		// curl -X GET "http://127.0.0.1:8001/api/suppliers/with-components" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('inactive', [SupplierController::class, 'inactive']);
		// curl -X GET "http://127.0.0.1:8001/api/suppliers/inactive" -H "Authorization: Bearer YOUR_TOKEN"
    });
    Route::apiResource('suppliers', SupplierController::class);
	// curl -X GET    "http://127.0.0.1:8001/api/suppliers"        -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X POST   "http://127.0.0.1:8001/api/suppliers"        -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"supplier_name":"Acme Parts","contact_person":"Alice","phone_number":"09991234567","email":"sales@acme.test"}'
	// curl -X GET    "http://127.0.0.1:8001/api/suppliers/1"      -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X PATCH  "http://127.0.0.1:8001/api/suppliers/1"      -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"contact_person":"Bob"}'
	// curl -X DELETE "http://127.0.0.1:8001/api/suppliers/1"      -H "Authorization: Bearer YOUR_TOKEN"
});

// Hardware Component API Routes (protected)
Route::middleware(['auth:sanctum'])->as('api.')->group(function () {
    Route::prefix('hardware-components')->group(function () {
        Route::get('category/{category}', [HardwareComponentController::class, 'byCategory']);
		// curl -X GET "http://127.0.0.1:8001/api/hardware-components/category/1" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('supplier/{supplier}', [HardwareComponentController::class, 'bySupplier']);
		// curl -X GET "http://127.0.0.1:8001/api/hardware-components/supplier/1" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('low-stock', [HardwareComponentController::class, 'lowStock']);
		// curl -X GET "http://127.0.0.1:8001/api/hardware-components/low-stock" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('ordered/{start}/{end}', [HardwareComponentController::class, 'orderedBetweenDates']);
		// curl -X GET "http://127.0.0.1:8001/api/hardware-components/ordered/2025-10-01/2025-10-31" -H "Authorization: Bearer YOUR_TOKEN"
    });
    Route::apiResource('hardware-components', HardwareComponentController::class);
	// curl -X GET    "http://127.0.0.1:8001/api/hardware-components"   -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X POST   "http://127.0.0.1:8001/api/hardware-components"   -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"component_reference_number":"GPU-0001","component_name":"RTX 4060","brand":"NVIDIA","model":"Twin","specifications":"8GB GDDR6","retail_price":299.99,"seller_price":250.00,"category_id":1,"supplier_id":1,"date_created":"2025-10-27"}'
	// curl -X GET    "http://127.0.0.1:8001/api/hardware-components/1" -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X PATCH  "http://127.0.0.1:8001/api/hardware-components/1" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"retail_price":289.99}'
	// curl -X DELETE "http://127.0.0.1:8001/api/hardware-components/1" -H "Authorization: Bearer YOUR_TOKEN"
});

// Order API Routes (protected)
Route::middleware(['auth:sanctum'])->as('api.')->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('buyer/{buyer}', [OrderController::class, 'byBuyer']);
		// curl -X GET "http://127.0.0.1:8001/api/orders/buyer/1" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('status/{status}', [OrderController::class, 'byStatus']);
		// curl -X GET "http://127.0.0.1:8001/api/orders/status/pending" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('date/{start}/{end}', [OrderController::class, 'byDateRange']);
		// curl -X GET "http://127.0.0.1:8001/api/orders/date/2025-10-01/2025-10-31" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('statistics', [OrderController::class, 'statistics']);
		// curl -X GET "http://127.0.0.1:8001/api/orders/statistics" -H "Authorization: Bearer YOUR_TOKEN"

        // Linking: /api/orders/{order}/items
        Route::get('{order}/items', [SelectedComponentController::class, 'byOrder']);
		// curl -X GET "http://127.0.0.1:8001/api/orders/1/items" -H "Authorization: Bearer YOUR_TOKEN"
    });
    Route::apiResource('orders', OrderController::class);
	// curl -X GET    "http://127.0.0.1:8001/api/orders"         -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X POST   "http://127.0.0.1:8001/api/orders"         -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"order_date":"2025-10-27","total_amount":500.00,"shipping_status":"pending","estimated_delivery":"2025-10-30","order_reference_number":"ORD-10001","buyer_id":1,"admin_id":1,"payment_method":"cash","selected_components":[{"component_id":1,"quantity":2}]}'
	// curl -X GET    "http://127.0.0.1:8001/api/orders/1"       -H "Authorization: Bearer YOUR_TOKEN"
	// curl -X PATCH  "http://127.0.0.1:8001/api/orders/1"       -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"shipping_status":"shipped"}'
	// curl -X DELETE "http://127.0.0.1:8001/api/orders/1"       -H "Authorization: Bearer ADMIN_TOKEN"
});

// Shop API Routes
Route::prefix('shop')->group(function () {
    // Public shop browse
    Route::get('/', [App\Http\Controllers\API\ShopController::class, 'index'])->name('api.shop.index');
    
    // Protected buyer routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/cart', [App\Http\Controllers\API\ShopController::class, 'cart'])->name('api.shop.cart');
        Route::post('/add', [App\Http\Controllers\API\ShopController::class, 'addToCart'])->name('api.shop.add');
        Route::delete('/remove/{id}', [App\Http\Controllers\API\ShopController::class, 'removeFromCart'])->name('api.shop.remove');
        Route::post('/place', [App\Http\Controllers\API\ShopController::class, 'placeOrder'])->name('api.shop.place');
        Route::get('/orders', [App\Http\Controllers\API\ShopController::class, 'myOrders'])->name('api.shop.orders');
    });
});

// Selected Component API Routes (protected)
Route::middleware(['auth:sanctum'])->group(function () {
    // Keep read-only list
    Route::apiResource('selected-components', SelectedComponentController::class)->only(['index']);
	// curl -X GET "http://127.0.0.1:8001/api/selected-components" -H "Authorization: Bearer YOUR_TOKEN"
    // Linking and analytics
    Route::prefix('selected-components')->group(function () {
        Route::get('order/{order}', [SelectedComponentController::class, 'byOrder']);
		// curl -X GET "http://127.0.0.1:8001/api/selected-components/order/1" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('component/{component}', [SelectedComponentController::class, 'ordersWithComponent']);
		// curl -X GET "http://127.0.0.1:8001/api/selected-components/component/1" -H "Authorization: Bearer YOUR_TOKEN"

        Route::get('most-ordered', [SelectedComponentController::class, 'mostOrdered']);
		// curl -X GET "http://127.0.0.1:8001/api/selected-components/most-ordered" -H "Authorization: Bearer YOUR_TOKEN"
    });
    // Nested CRUD for order items (Selected Components)
    Route::prefix('orders/{order}')->group(function () {
        Route::get('items', [SelectedComponentController::class, 'byOrder']);
		// curl -X GET "http://127.0.0.1:8001/api/orders/1/items" -H "Authorization: Bearer YOUR_TOKEN"
        Route::post('items', [SelectedComponentController::class, 'storeForOrder']);
		// curl -X POST "http://127.0.0.1:8001/api/orders/1/items" \
		//   -H "Authorization: Bearer YOUR_TOKEN" \
		//   -H "Content-Type: application/json" \
		//   -d '{"component_id":1,"quantity":1}'
        Route::put('items/{selectedComponent}', [SelectedComponentController::class, 'update']);
		// curl -X PUT "http://127.0.0.1:8001/api/orders/1/items/1" \
		//   -H "Authorization: Bearer YOUR_TOKEN" \
		//   -H "Content-Type: application/json" \
		//   -d '{"quantity":3}'
        Route::patch('items/{selectedComponent}', [SelectedComponentController::class, 'update']);
		// curl -X PATCH "http://127.0.0.1:8001/api/orders/1/items/1" \
		//   -H "Authorization: Bearer YOUR_TOKEN" \
		//   -H "Content-Type: application/json" \
		//   -d '{"quantity":3}'
        Route::delete('items/{selectedComponent}', [SelectedComponentController::class, 'destroy']);
		// curl -X DELETE "http://127.0.0.1:8001/api/orders/1/items/1" \
		//   -H "Authorization: Bearer YOUR_TOKEN"
    });
});
