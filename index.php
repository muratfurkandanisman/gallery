<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/core/Helpers.php';

$db = null;
$dbError = null;

try {
		$db = Database::getInstance($config['db']);
} catch (Throwable $e) {
		$dbError = $e->getMessage();
}

function isApiPath(string $path): bool
{
		return str_starts_with($path, '/api/');
}

function ensureDbOrFail($db): void
{
		if ($db === null) {
				Response::error('Veritabani baglantisi kurulamadigi icin API su an kullanilamiyor.', 500);
		}
}

$path = Request::path();
$method = Request::method();

if (isApiPath($path)) {
		ensureDbOrFail($db);

		$userRepo = new UserRepository($db);
		$carRepo = new CarRepository($db);
		$favRepo = new FavoriteRepository($db);
		$inqRepo = new InquiryRepository($db);
		$chatRepo = new ChatRepository($db);

		$authController = new AuthController(new AuthService($userRepo));
		$carController = new CarController(new CarService($carRepo));
		$favoriteController = new FavoriteController(new FavoriteService($favRepo, $carRepo));
		$inquiryController = new InquiryController(new InquiryService($inqRepo, $carRepo));
		$chatController = new ChatController(new ChatService($chatRepo, $carRepo));

		if ($path === '/api/auth/register' && $method === 'POST') {
				$authController->register();
		}
		if ($path === '/api/auth/login' && $method === 'POST') {
				$authController->login();
		}
		if ($path === '/api/auth/logout' && $method === 'POST') {
				$authController->logout();
		}
		if ($path === '/api/auth/me' && $method === 'GET') {
				$authController->me();
		}

		if ($path === '/api/cars' && $method === 'GET') {
				$carController->list();
		}
		if (preg_match('#^/api/cars/(\d+)$#', $path, $m) && $method === 'GET') {
				$carController->detail((int) $m[1]);
		}

		if ($path === '/api/favorites' && $method === 'GET') {
				$favoriteController->list();
		}
		if (preg_match('#^/api/favorites/(\d+)$#', $path, $m) && $method === 'POST') {
				$favoriteController->toggle((int) $m[1]);
		}
		if (preg_match('#^/api/favorites/(\d+)$#', $path, $m) && $method === 'DELETE') {
				$favoriteController->remove((int) $m[1]);
		}

		if ($path === '/api/inquiries' && $method === 'POST') {
				$inquiryController->create();
		}

		if ($path === '/api/chats/start' && $method === 'POST') {
				$chatController->start();
		}
		if ($path === '/api/chats' && $method === 'GET') {
				$chatController->list();
		}
		if (preg_match('#^/api/chats/(\d+)/messages$#', $path, $m) && $method === 'GET') {
				$chatController->messages((int) $m[1]);
		}
		if (preg_match('#^/api/chats/(\d+)/messages$#', $path, $m) && $method === 'POST') {
				$chatController->send((int) $m[1]);
		}
		if (preg_match('#^/api/admin/chats/(\d+)/close$#', $path, $m) && $method === 'POST') {
				$chatController->close((int) $m[1]);
		}

		if ($path === '/api/admin/cars' && $method === 'GET') {
				$carController->adminList();
		}
		if ($path === '/api/admin/cars' && $method === 'POST') {
				$carController->adminCreate();
		}
		if (preg_match('#^/api/admin/cars/(\d+)$#', $path, $m) && $method === 'PUT') {
				$carController->adminUpdate((int) $m[1]);
		}
		if (preg_match('#^/api/admin/cars/(\d+)/mark-sold$#', $path, $m) && $method === 'POST') {
				$carController->adminMarkSold((int) $m[1]);
		}
		if (preg_match('#^/api/admin/cars/(\d+)$#', $path, $m) && $method === 'DELETE') {
				$carController->adminDelete((int) $m[1]);
		}

		if ($path === '/api/admin/inquiries' && $method === 'GET') {
				$inquiryController->adminList();
		}
		if (preg_match('#^/api/admin/inquiries/(\d+)$#', $path, $m) && $method === 'PUT') {
				$inquiryController->adminUpdateStatus((int) $m[1]);
		}

		Response::error('Endpoint bulunamadi.', 404);
}

$baseUrl = env_base_url();

if ($path === '/' || $path === '/showroom') {
		require __DIR__ . '/app/views/showroom.php';
		exit;
}

if ($path === '/access') {
		require __DIR__ . '/app/views/access.php';
		exit;
}

if ($path === '/messages') {
		if (!Auth::check()) {
			header('Location: ' . $baseUrl . '/access');
			exit;
		}

		require __DIR__ . '/app/views/messages.php';
		exit;
}

if (preg_match('#^/vehicle/(\d+)$#', $path, $m)) {
		$carId = (int) $m[1];
		require __DIR__ . '/app/views/detail.php';
		exit;
}

if ($path === '/admin') {
		if (!Auth::check()) {
			header('Location: ' . $baseUrl . '/access');
			exit;
		}

		if (!Auth::isAdmin()) {
			http_response_code(403);
			echo 'Bu sayfaya erisim yetkiniz yok.';
			exit;
		}

		require __DIR__ . '/app/views/admin.php';
		exit;
}

if (preg_match('#^/admin/cars/(\d+)/edit$#', $path, $m)) {
		if (!Auth::check()) {
			header('Location: ' . $baseUrl . '/access');
			exit;
		}

		if (!Auth::isAdmin()) {
			http_response_code(403);
			echo 'Bu sayfaya erisim yetkiniz yok.';
			exit;
		}

		$carId = (int) $m[1];
		require __DIR__ . '/app/views/admin-edit.php';
		exit;
}

if ($path === '/playground') {
		require __DIR__ . '/app/views/playground.php';
		exit;
}

http_response_code(404);
echo 'Sayfa bulunamadi.';