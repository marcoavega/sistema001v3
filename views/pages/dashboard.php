<?php
// Archivo: views/pages/dashboard.php

// Verificación de sesión
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['user'])) {
  header("Location: " . BASE_URL . "auth/login/");
  exit();
}

// Obtener segmento de URL para destacar menú activo
$uri = $_GET['url'] ?? 'dashboard';
$segment = explode('/', trim($uri, '/'))[0];

// Iniciar buffer de salida
ob_start();

// Nombre de usuario para mostrar
$username = htmlspecialchars($_SESSION['user']['username']);



require_once __DIR__ . '/../../models/Database.php';
$pdo = (new Database())->getConnection();

// Contar usuarios
$stmtUsers = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmtUsers->fetch()['total_users'] ?? 0;

// Contar productos
$stmtProducts = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
$totalProducts = $stmtProducts->fetch()['total_products'] ?? 0;


// Incluir menú lateral de productos/inventario
require_once __DIR__ . '/../partials/layouts/lateral_menu_dashboard.php';
?>

<div class="container-fluid m-0 p-0 min-vh-100" data-bs-theme="auto">
  <div class="row g-0">

    <!-- Barra lateral con gradiente moderno -->
    <nav class="col-md-2 d-none d-md-block sidebar min-vh-100">
      <div class="pt-4 px-3">
        <div class="text-center mb-4">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="bi bi-speedometer2 text-primary fs-3"></i>
          </div>
          <h6 class="mt-2 mb-0">Sistema</h6>
        </div>
        <ul class="nav flex-column">
          <?php foreach ($menuItems as $route => $item): ?>
            <li class="nav-item mb-2">
              <a
                class="nav-link d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'bg-primary text-white fw-bold' : 'text-body' ?>"
                href="<?= BASE_URL . $route ?>"
                style="transition: all 0.3s ease;">
                <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i>
                <span class="fw-medium"><?= $item['label'] ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </nav>

    <!-- Contenido principal -->
    <main class="col-12 col-md-10">

      <!-- Header con breadcrumb moderno -->
      <div class="bg-body shadow-sm border-bottom">
        <div class="container-fluid px-4 py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-decoration-none">Dashboard</a></li>
                </ol>
              </nav>
              <h4 class="mb-0 fw-bold">Panel de Control</h4>
              <small class="text-muted">Bienvenido, <?= $username ?></small>
            </div>
            <div class="d-md-none">
              <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="bi bi-list"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Menú móvil offcanvas -->
      <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header bg-primary-subtle">
          <h5 class="offcanvas-title"><i class="bi bi-speedometer2 me-2"></i>Sistema</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body bg-body">
          <ul class="nav flex-column">
            <?php foreach ($menuItems as $route => $item): ?>
              <li class="nav-item mb-2">
                <a
                  class="nav-link text-body d-flex align-items-center px-3 py-2 rounded-3 <?= $segment === $route ? 'active bg-primary text-white' : '' ?>"
                  href="<?= BASE_URL . $route ?>">
                  <i class="bi bi-<?= $item['icon'] ?> me-3 fs-5"></i>
                  <?= $item['label'] ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <div class="container-fluid px-4 py-4">

        <!-- Estadísticas rápidas -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="card shadow-sm border-0">
              <div class="card-body text-center">
                <i class="bi bi-people-fill fs-1 text-primary mb-2"></i>
                <h6>Usuarios</h6>
                <h3><?= $totalUsers ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0">
              <div class="card-body text-center">
                <i class="bi bi-box-seam fs-1 text-success mb-2"></i>
                <h6>Productos</h6>
                <h3><?= $totalProducts ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0">
              <div class="card-body text-center">
                <i class="bi bi-cart-check fs-1 text-warning mb-2"></i>
                <h6>Órdenes</h6>
                <h3>--</h3>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0">
              <div class="card-body text-center">
                <i class="bi bi-graph-up fs-1 text-danger mb-2"></i>
                <h6>Reportes</h6>
                <h3>--</h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de actividad reciente con Tabulator -->
        <!-- Tabla de actividad reciente con Tabulator -->
        <div class="card shadow-sm border-0">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-md-6">
                <h5 class="mb-0">Actividad Reciente</h5>
              </div>

             <!-- Buscador + Exportar -->
<div class="row g-2 justify-content-md-end align-items-center mb-3">
  <div class="col-auto">
    <div class="position-relative">
      <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
      <input
        type="text"
        id="table-search"
        class="form-control form-control-sm ps-5 rounded-pill border-2"
        placeholder="Buscar por fecha, usuario o acción…"
      >
    </div>
  </div>
  <div class="col-auto">
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle rounded-pill px-3" data-bs-toggle="dropdown">
        <i class="bi bi-download me-1"></i>Exportar
      </button>
      <ul class="dropdown-menu shadow-lg border-0 rounded-3">
        <li><h6 class="dropdown-header fw-bold">Formatos disponibles</h6></li>
        <li><button id="exportCSVBtn"   class="dropdown-item"><i class="bi bi-filetype-csv text-success me-2"></i>CSV</button></li>
        <li><button id="exportExcelBtn" class="dropdown-item"><i class="bi bi-file-earmark-excel text-success me-2"></i>XLSX</button></li>
        <li><button id="exportPDFBtn"   class="dropdown-item"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>PDF</button></li>
        <li><button id="exportJSONBtn"  class="dropdown-item"><i class="bi bi-filetype-json text-info me-2"></i>JSON</button></li>
      </ul>
    </div>
  </div>
</div>
              <!-- fin buscador + export -->

            </div>
          </div>
          <div class="card-body p-0">
            <div id="recent-activity-table" class="border rounded-3"></div>
          </div>
        </div>


      </div>
    </main>
  </div>
</div>

<script src="<?= BASE_URL ?>assets/js/ajax/dashboard-activity.js"></script>

<style>
  /* Misma transición lateral */
  .sidebar .nav-link:hover {
    transform: translateX(5px);
  }

  /* Cards levitan */
  .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .card:hover {
    transform: translateY(-2px);
  }

  /* Tabulator estilo base */
  .tabulator {
    border: none !important;
    background: transparent !important;
  }

  .tabulator-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
    border: none !important;
  }

  .tabulator-row:hover {
    background-color: rgba(13, 110, 253, 0.1) !important;
  }

  .tabulator-selected {
    background-color: rgba(13, 110, 253, 0.2) !important;
  }

  /* Responsive offcanvas text */
  @media (max-width: 767px) {
    .offcanvas-body .nav-link {
      padding-left: 1rem;
    }
  }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>
