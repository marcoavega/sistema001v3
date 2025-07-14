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
                style="transition: all 0.3s ease;"
              >
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
                  href="<?= BASE_URL . $route ?>"
                >
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
        <div class="card shadow-sm border-0">
          <div class="card-header">
            <h5 class="mb-0">Actividad Reciente</h5>
          </div>
          <div class="card-body p-0">
            <div id="recent-activity-table" class="border rounded-3"></div>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<style>
  /* Misma transición lateral */
  .sidebar .nav-link:hover { transform: translateX(5px); }

  /* Cards levitan */
  .card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
  .card:hover { transform: translateY(-2px); }

  /* Tabulator estilo base */
  .tabulator { border: none !important; background: transparent !important; }
  .tabulator-header { background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important; border: none !important; }
  .tabulator-row:hover { background-color: rgba(13,110,253,0.1) !important; }
  .tabulator-selected { background-color: rgba(13,110,253,0.2) !important; }

  /* Responsive offcanvas text */
  @media (max-width: 767px) {
    .offcanvas-body .nav-link { padding-left: 1rem; }
  }


  
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layouts/navbar.php';
?>



<script>
document.addEventListener('DOMContentLoaded', () => {
  
  // Inyectar CSS para scroll + no wrapping (igual que tabla de productos)
  const style = document.createElement("style");
  style.textContent = `
    /* No permitir salto de línea en celdas/headers */
    .tabulator .tabulator-col,
    .tabulator .tabulator-cell {
      white-space: nowrap !important;
    }

    /* Contenedor Tabulator */
    .tabulator {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      width: 100%;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      position: relative;
      margin-bottom: 30px;
    }

    .tabulator-table {
      min-width: 500px; /* Ajusta según tus columnas */
      touch-action: pan-x;
      width: 100% !important;
      border-spacing: 0;
    }

    .tabulator-col:empty,
    .tabulator-cell:empty:not([data-field]) {
      display: none !important;
    }

    .tabulator-header {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 2px solid #dee2e6;
    }

    .tabulator-row:hover {
      background-color: #f8f9fa !important;
    }

    /* Scrollbars móviles */
    @media (max-width: 767px) {
      .tabulator::-webkit-scrollbar { height: 12px; }
      .tabulator::-webkit-scrollbar-track { background: #f1f1f1; border-radius:6px; }
      .tabulator::-webkit-scrollbar-thumb { background: #007bff; border-radius:6px; border:2px solid #f1f1f1; }
      .tabulator::-webkit-scrollbar-thumb:hover { background: #0056b3; }
      .tabulator::after {
        content: "← Desliza para ver más columnas →";
        position: absolute; bottom: -25px; left: 50%;
        transform: translateX(-50%); font-size:12px;
        color:#007bff; font-weight:500; pointer-events:none;
      }
      .tabulator-cell { padding: 8px 6px !important; font-size:13px; }
      .tabulator-col  { padding:10px 6px !important; font-size:12px; font-weight:600; }
    }
    /* Scrollbars escritorio */
    @media (min-width: 768px) {
      .tabulator::-webkit-scrollbar { height: 8px; }
      .tabulator::-webkit-scrollbar-track { background: #f1f1f1; border-radius:4px; }
      .tabulator::-webkit-scrollbar-thumb { background: #888; border-radius:4px; }
      .tabulator::-webkit-scrollbar-thumb:hover { background: #555; }
    }
  `;
  document.head.appendChild(style);

  // Inicializar Tabulator igual que productos
  new Tabulator("#recent-activity-table", {
    layout: "fitColumns",
    placeholder: "Cargando actividad reciente...",

    // Paginación remota
    pagination: "remote",
    paginationSize: 10,
    paginationSizeSelector: [10, 20, 50, 100],
    paginationButtonCount: 5,

    // AJAX
    ajaxURL: BASE_URL + "api/logs.php",
    ajaxConfig: "GET",
    paginationDataSent: { page: "page", size: "size" },
    paginationDataReceived: { last_page: "last_page", data: "data" },

    ajaxRequesting: () => {
      document.querySelector("#recent-activity-table").style.opacity = "0.6";
    },
    ajaxResponse: (_,__,response) => {
      document.querySelector("#recent-activity-table").style.opacity = "1";
      return response.data || [];
    },

    // Columnas
   columns: [
    {
      title: "Fecha",
      field: "timestamp",
      sorter: "datetime",
      hozAlign: "center",
      formatter: cell => {
        let d = new Date(cell.getValue());
        return isNaN(d) ? cell.getValue() : d.toLocaleString("es-ES");
      },
      widthGrow: 1,  // <— todas las columnas con widthGrow:1
    },
    {
      title: "Usuario",
      field: "username",
      hozAlign: "center",
      widthGrow: 1,
    },
    {
      title: "Acción",
      field: "action",
      hozAlign: "left",
      widthGrow: 1,
    },
  ],

    // Comportamiento extra
    headerSort: true,
    headerSortTristate: true,
    movableColumns: false,
    resizableColumns: true,
    tooltips: true,
    rowClick: () => {},
  });
});
</script>



