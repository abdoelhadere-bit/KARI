<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../core/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';


use utils\Session;
use services\AuthService;

Session::start();

$page = $_GET['page'] ?? 'home';

$viewPath = dirname(__DIR__) . '/views/';

try{
    switch ($page) {
        case 'login':
            require $viewPath . 'login.php';
            break;
    
        case 'register':
            require $viewPath . 'register.php';
            break;
    
        case 'profile':
            require $viewPath . 'profile.php';
            break;
    
        case 'rental':
            require $viewPath . 'rental_show.php';
            break;
    
        case 'dashboard_traveler':
            require $viewPath . 'dashboard_traveler.php';
            break;
    
        case 'dashboard_host':
            require $viewPath . 'dashboard_host.php';
            break;
    
        case 'dashboard_admin':
            require $viewPath . 'dashboard_admin.php';
            break;
    
        case 'host_rentals':
            require $viewPath . 'host_rentals.php';
            break;
    
        case 'logout':
            (new AuthService())->logout();
            header("Location: index.php?page=login");
            exit;
            
        case 'admin_panel':
            require $viewPath . 'admin_panel.php';
            break;

        case 'admin_reservations':
            require $viewPath . 'admin_reservations.php';
            break;
                
        case 'admin_cancel_reservation':
            require $viewPath . 'admin_cancel_reservation.php';
            break;
        
        case 'book':
            require $viewPath . 'book.php';
            break;

        case 'my_reservations':
            require $viewPath . 'my_reservations.php';
            break;

        case 'reservation_pdf':
            require $viewPath . 'reservation_pdf.php';
            break;

        case 'cancel':
            require $viewPath . 'cancel.php';
            break;
            
        case 'my_favorites':
            require $viewPath . 'my_favorites.php';
            break;
                
        case 'favorite_toggle':
            require $viewPath . 'favorite_toggle.php';
            break;
        
        case 'review_create':
            require $viewPath . 'review_create.php';
            break;

        default:
            require $viewPath . 'home.php';
            break;
}
}catch (Throwable $e) {
    echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
}
