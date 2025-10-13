// Espera a que todo el DOM esté cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', () => {

  // 1) Inyectar estilos CSS para personalizar la tabla de Tabulator
  // Esto asegura que la tabla de actividad tenga el mismo estilo que la de productos
  const style = document.createElement("style");
  style.textContent = `
    /* Evita que el texto de las celdas se rompa en varias líneas */
    .tabulator .tabulator-col,
    .tabulator .tabulator-cell { white-space: nowrap !important; }

    /* Estilos generales de la tabla */
    .tabulator {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch; /* Mejor desplazamiento en móviles */
      width: 100%;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    /* Mínimo ancho para evitar que las columnas se junten demasiado */
    .tabulator-table {
      min-width: 600px;
      touch-action: pan-x;
      width: 100% !important;
    }

    /* Encabezado con degradado */
    .tabulator-header {
      background: linear-gradient(135deg,#f8f9fa,#e9ecef);
      border-bottom: 2px solid #dee2e6;
    }

    /* Efecto hover en filas */
    .tabulator-row:hover { background-color: #f8f9fa !important; }

    /* Estilos para pantallas pequeñas */
    @media (max-width: 767px) {
      .tabulator::-webkit-scrollbar { height: 12px; }
      .tabulator::-webkit-scrollbar-thumb { background: #007bff; }
      
      /* Mensaje para indicar desplazamiento horizontal */
      .tabulator::after {
        content: "← Desliza para ver más columnas →";
        position: absolute; bottom: -25px; left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        color: #007bff;
        font-weight: 500;
      }

      /* Ajusta el padding y tamaño de texto */
      .tabulator-cell { padding: 8px 6px !important; font-size: 13px; }
      .tabulator-col  { padding: 10px 6px !important; font-size: 12px; font-weight: 600; }
    }

    /* Scrollbar más pequeño en escritorio */
    @media (min-width: 768px) {
      .tabulator::-webkit-scrollbar { height: 8px; }
    }
  `;
  document.head.appendChild(style);

  // 2) Crear instancia de la tabla Tabulator
  const activityTable = new Tabulator("#recent-activity-table", {
    layout:               "fitColumns", // Ajusta las columnas para que encajen
    placeholder:          "Cargando actividad reciente…", // Texto mientras carga
    pagination:           "remote", // Paginación controlada por el servidor
    paginationSize:       20, // Registros por página
    paginationSizeSelector: [10,20,50,100], // Opciones de selección de tamaño
    paginationButtonCount: 5, // Número de botones de paginación visibles

    // URL desde donde se cargan los datos
    ajaxURL:    BASE_URL + "api/logs.php",
    ajaxConfig: "GET", // Método HTTP

    // Mapeo de parámetros de paginación enviados al servidor
    paginationDataSent:     { page:"page", size:"size" },
    // Mapeo de lo que el servidor devuelve
    paginationDataReceived: { last_page:"last_page", data:"data" },

    // Evento antes de hacer la petición AJAX
    ajaxRequesting: () => {
      document.querySelector("#recent-activity-table").style.opacity = "0.6";
    },
    // Evento después de recibir la respuesta del servidor
    ajaxResponse: (_url,_params,response) => {
      document.querySelector("#recent-activity-table").style.opacity = "1";
      // Devuelve los datos o un array vacío si no hay
      return Array.isArray(response.data) ? response.data : [];
    },

    // Definición de las columnas
    columns: [
      {
        title:     "Fecha",
        field:     "timestamp",
        sorter:    "datetime", // Ordenar por fecha
        hozAlign:  "center",
        formatter: cell => { // Formatear fecha al formato local español
          const d = new Date(cell.getValue());
          return isNaN(d) ? cell.getValue() : d.toLocaleString("es-ES");
        },
        widthGrow: 1,
      },
      { title:"Usuario", field:"username", hozAlign:"center", widthGrow:1 },
      { title:"Acción",  field:"action",   hozAlign:"left",   widthGrow:1 },
    ],

    // Otras configuraciones de interacción
    headerSort:         true,
    headerSortTristate: true, // Orden asc/desc/ninguno
    movableColumns:     false,
    resizableColumns:   true,
    tooltips:           true, // Muestra tooltip con contenido de celda
  });

  // 3) Botones para exportar en distintos formatos
  document.getElementById("exportCSVBtn").addEventListener("click", () => {
    activityTable.download("csv",  "actividad_reciente.csv");
  });
  document.getElementById("exportExcelBtn").addEventListener("click", () => {
    activityTable.download("xlsx", "actividad_reciente.xlsx", {
      sheetName: "Actividad"
    });
  });
  document.getElementById("exportPDFBtn").addEventListener("click", () => {
    activityTable.download("pdf", "actividad_reciente.pdf", {
      orientation: "portrait",
      title: "Actividad Reciente"
    });
  });
  document.getElementById("exportJSONBtn").addEventListener("click", () => {
    activityTable.download("json", "actividad_reciente.json");
  });

  // 4) Función helper: debounce
  // Sirve para que la búsqueda no se ejecute en cada tecla,
  // sino después de un pequeño retraso (delay)
  function debounce(fn, delay) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  // 5) Configuración del buscador
  // Filtra los datos y recarga la tabla empezando desde la página 1
  const input = document.getElementById("table-search");
  if (!input) {
    console.error("No encontré el input #table-search");
    return;
  }

  input.addEventListener("keyup", debounce(function() {
    const term = this.value.trim();
    // Ir a la página 1 antes de recargar
    activityTable.setPage(1).then(() => {
      if (term === "") {
        // Si no hay término de búsqueda, recargar sin filtros
        activityTable.setData();
      } else {
        // Si hay búsqueda, pasar el parámetro search al servidor
        activityTable.setData(undefined, { search: term });
      }
    });
  }, 300)); // Retraso de 300ms para optimizar
});
