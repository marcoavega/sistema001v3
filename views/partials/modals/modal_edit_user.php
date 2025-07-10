<!-- Modal para la edición de usuario, utilizando Bootstrap -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <!-- 'modal fade' añade un efecto de desvanecimiento cuando el modal se abre o se cierra.
         'id="editUserModal"' permite identificar y manipular el modal con JavaScript.
         'tabindex="-1"' evita que el modal reciba foco por teclado cuando está oculto.
         'aria-labelledby="editUserModalLabel"' mejora la accesibilidad, asociando el modal con su título.
         'aria-hidden="true"' indica que el modal está oculto inicialmente para los lectores de pantalla. -->

    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <!-- 'modal-dialog' es el contenedor principal del modal, definiendo su estructura.
             'modal-xl' para tamaño extra grande.
             'modal-dialog-centered' centra verticalmente el modal.
             'modal-dialog-scrollable' agrega scroll si el contenido es muy largo. -->

        <div class="modal-content border-0 shadow-lg">
            <!-- 'modal-content' agrupa todos los elementos dentro del modal: encabezado, cuerpo y pie de página.
                 'border-0 shadow-lg' para el estilo similar al modal de producto. -->

            <div class="modal-header bg-gradient position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <i class="fas fa-user-edit" style="font-size: 120px;"></i>
                    </div>
                </div>
                <div class="position-relative">
                    <h4 class="modal-title fw-bold mb-0" id="editUserModalLabel">
                        <i class="fas fa-edit me-2"></i>
                        Editar Usuario
                    </h4>
                    <small class="opacity-75">Modifique la información del usuario</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Contenido principal del modal, donde se muestra el formulario de edición de usuario. -->
                <form id="editUserForm">
                    <!-- Formulario con 'id="editUserForm"' para identificarlo y procesarlo con JavaScript. -->

                    <input type="hidden" id="edit-user-id">
                    <!-- Campo oculto que almacena el ID del usuario a editar. Este dato se utiliza para saber qué usuario se está modificando. -->

                    <div class="mb-3">
                        <label for="edit-username" class="form-label">Usuario</label>
                        <!-- Etiqueta del campo de texto para editar el nombre de usuario. -->
                        <input type="text" class="form-control" id="edit-username">
                        <!-- Campo de texto para ingresar el nuevo nombre de usuario.
                             'class="form-control"' aplica los estilos de Bootstrap. -->
                    </div>

                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <!-- Etiqueta del campo de email. -->
                        <input type="email" class="form-control" id="edit-email">
                        <!-- Campo de texto para ingresar el nuevo email del usuario.
                             'type="email"' asegura que la entrada siga el formato de correo electrónico. -->
                    </div>

                    <div class="mb-3">
                        <label for="edit-level" class="form-label">Nivel</label>
                        <!-- Etiqueta para el campo de selección del nivel de usuario. -->
                        <select id="edit-level" class="form-select">
                            <!-- Menú desplegable para seleccionar el nivel del usuario.
                                 'class="form-select"' aplica estilos de Bootstrap para el campo de selección. -->

                            <?php foreach ($levels as $lvl): ?>
                                <!-- Itera sobre el arreglo $levels, que contiene los niveles de usuario disponibles. -->
                                <option value="<?= $lvl['id_level_user'] ?>">
                                    <?= htmlspecialchars($lvl['description_level']) ?>
                                    <!-- Muestra la descripción del nivel, asegurando que el texto se escape con 'htmlspecialchars()' para evitar inyecciones de código. -->
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <!-- Pie de página del modal donde se incluyen los botones de acción. -->

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <!-- Botón de cancelar que cierra el modal sin guardar cambios.
                     'btn-secondary' le da un color gris para diferenciarlo del botón de guardar. -->

                <button type="button" class="btn btn-primary" id="saveChangesBtn">Guardar cambios</button>
                <!-- Botón para guardar los cambios del usuario.
                     'id="saveChangesBtn"' permite identificarlo y manipularlo con JavaScript. -->
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus, .form-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.modal.show .modal-dialog {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.btn {
  transition: all 0.2s ease-in-out;
}

.btn:hover {
  transform: translateY(-1px);
}
</style>
