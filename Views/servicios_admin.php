<?php
session_start();

if (!isset($_SESSION['logueado']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

require_once "../Controller/ServicioController.php";
$controller = new ServicioController();
$servicios = $controller->listar();

$titulo_pagina = "Gestión de Servicios";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($titulo_pagina); ?></title>

  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="css/dashboard_admin.css">
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;900&display=swap" rel="stylesheet"/>

  <style>
    .modal { display:none; }
    .modal.activo { display:flex; }
  </style>
</head>

<body>
  <div class="contenedor">

    <!-- ======= SIDEBAR ORIGINAL ======= -->
    <?php include 'partials/admin_sidebar.php'; ?>

    <!-- ======= CONTENIDO PRINCIPAL ======= -->
    <main class="contenido-principal">

      <header class="cabecera-principal">
        <h1><?= htmlspecialchars($titulo_pagina); ?></h1>
        <div class="info-usuario">
          <img src="../img/logo.jpg" class="avatar">
          <div>
            <p class="nombre-usuario"><?= $_SESSION['usuario_nombre'] ?? 'Admin'; ?></p>
            <p class="rol-usuario">Administrador</p>
          </div>
        </div>
      </header>

      <section class="contenido-pagina">

        <div class="tarjeta-tabla">

          <div class="tarjeta-cabecera">
            <h2>Listado de Servicios</h2>

            <button id="btnAbrirModalAgregar"
              class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
              <i data-lucide="plus"></i> Nuevo Servicio
            </button>
          </div>

          <div class="overflow-x-auto mt-4">
            <table class="tabla-admin">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Precio</th>
                  <th>Categoría</th>
                  <th>Estado</th>
                  <th style="text-align:right;">Acciones</th>
                </tr>
              </thead>
              <tbody id="cuerpoTabla"></tbody>
            </table>
          </div>

        </div>
      </section>
    </main>
  </div>


<!-- ===================================================================================== -->
<!-- ========================== MODAL AGREGAR (DISEÑO NUEVO) ============================= -->
<!-- ===================================================================================== -->

<div id="modalAgregar" class="modal fixed inset-0 bg-black/40 items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl w-full max-w-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4 text-center text-blue-600">Agregar Servicio</h2>

    <form id="formAgregar" class="space-y-4">

      <input name="nombre" placeholder="Nombre del servicio" class="w-full form-input rounded-lg" required>

      <textarea name="descripcion" placeholder="Descripción" class="w-full form-input rounded-lg" required></textarea>

      <input name="precio" type="number" step="0.01" placeholder="Precio" class="w-full form-input rounded-lg" required>

      <select name="categoria" class="w-full form-select rounded-lg" required>
        <option value="">Seleccionar Categoría</option>
        <option>Hospitalización</option>
        <option>Anestesia Inhalatoria</option>
        <option>Medicina Regenerativa</option>
        <option>Medicina Preventiva</option>
        <option>Imagenología Ecografía</option>
        <option>Imagenología RX</option>
        <option>Cirugías</option>
        <option>Spa Canino</option>
      </select>

      <select name="estado" class="w-full form-select rounded-lg" required>
        <option>Activo</option>
        <option>Inactivo</option>
      </select>

      <input name="fecha_inicio" type="date" class="w-full form-input rounded-lg" required>

      <div class="flex justify-end gap-3 mt-4">
        <button type="button" id="btnCancelarAgregar"
        class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Cancelar</button>

        <button type="submit"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar</button>
      </div>

    </form>
  </div>
</div>


<!-- ===================================================================================== -->
<!-- ========================== MODAL EDITAR (DISEÑO NUEVO) ============================== -->
<!-- ===================================================================================== -->

<div id="modalCrud" class="modal fixed inset-0 bg-black/40 items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl w-full max-w-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4 text-center text-blue-600">Editar Servicio</h2>

    <form id="formEditar" class="space-y-4">

      <input type="hidden" name="id" id="editar_id">

      <input id="editar_nombre" name="nombre" class="w-full form-input rounded-lg" required>

      <textarea id="editar_descripcion" name="descripcion" class="w-full form-input rounded-lg" required></textarea>

      <input id="editar_precio" name="precio" type="number" step="0.01" class="w-full form-input rounded-lg" required>

      <select id="editar_categoria" name="categoria" class="w-full form-select rounded-lg" required>
        <option>Hospitalización</option>
        <option>Anestesia Inhalatoria</option>
        <option>Medicina Regenerativa</option>
        <option>Medicina Preventiva</option>
        <option>Imagenología Ecografía</option>
        <option>Imagenología RX</option>
        <option>Cirugías</option>
        <option>Spa Canino</option>
      </select>

      <select id="editar_estado" name="estado" class="w-full form-select rounded-lg" required>
        <option>Activo</option>
        <option>Inactivo</option>
      </select>

      <input id="editar_fecha_inicio" name="fecha_inicio" type="date" class="w-full form-input rounded-lg" required>

      <div class="flex justify-between mt-4">

        <button type="button" id="btnEliminar"
        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Eliminar</button>

        <div class="flex gap-3">
          <button type="button" id="btnCancelarEditar"
          class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Cancelar</button>

          <button type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Actualizar</button>
        </div>

      </div>

    </form>
  </div>
</div>


<!-- ===================================================================================== -->
<!-- ================================== FUNCIONALIDAD JS ================================= -->
<!-- ===================================================================================== -->

<script>
lucide.createIcons();

const modalAgregar = document.getElementById("modalAgregar");
const modalCrud = document.getElementById("modalCrud");

const btnAbrirModalAgregar = document.getElementById("btnAbrirModalAgregar");
const btnCancelarAgregar = document.getElementById("btnCancelarAgregar");
const btnCancelarEditar = document.getElementById("btnCancelarEditar");

btnAbrirModalAgregar.onclick = () => modalAgregar.classList.add("activo");
btnCancelarAgregar.onclick = () => modalAgregar.classList.remove("activo");
btnCancelarEditar.onclick = () => modalCrud.classList.remove("activo");

const cuerpoTabla = document.getElementById("cuerpoTabla");

async function cargarServicios() {
  const res = await fetch("../Controller/ServicioController.php", {
    method: "POST",
    body: new URLSearchParams({ accion: "listar" })
  });
  const data = await res.json();

  cuerpoTabla.innerHTML = data.map(s => `
    <tr>
      <td>${s.nombre}</td>
      <td>${s.descripcion}</td>
      <td>S/ ${parseFloat(s.precio).toFixed(2)}</td>
      <td>${s.categoria}</td>
      <td>${s.estado}</td>
      <td style="text-align:right;">
        <button onclick="editarServicio(${s.id})" class="boton-link text-blue-600">
          <i data-lucide="edit"></i> Editar
        </button>
      </td>
    </tr>
  `).join("");

  lucide.createIcons();
}

document.getElementById("formAgregar").addEventListener("submit", async e => {
  e.preventDefault();
  const formData = new FormData(e.target);
  formData.append("accion", "guardar");

  await fetch("../Controller/ServicioController.php", {
    method: "POST",
    body: formData
  });

  modalAgregar.classList.remove("activo");
  e.target.reset();
  cargarServicios();
});

async function editarServicio(id) {
  const res = await fetch("../Controller/ServicioController.php", {
    method: "POST",
    body: new URLSearchParams({ accion: "obtener", id })
  });

  const s = await res.json();

  document.getElementById("editar_id").value = s.id;
  document.getElementById("editar_nombre").value = s.nombre;
  document.getElementById("editar_descripcion").value = s.descripcion;
  document.getElementById("editar_precio").value = s.precio;
  document.getElementById("editar_categoria").value = s.categoria;
  document.getElementById("editar_estado").value = s.estado;
  document.getElementById("editar_fecha_inicio").value = s.fecha_inicio;

  modalCrud.classList.add("activo");
  lucide.createIcons();
}

document.getElementById("formEditar").addEventListener("submit", async e => {
  e.preventDefault();
  const formData = new FormData(e.target);
  formData.append("accion", "actualizar");

  await fetch("../Controller/ServicioController.php", {
    method: "POST",
    body: formData
  });

  modalCrud.classList.remove("activo");
  cargarServicios();
});

document.getElementById("btnEliminar").addEventListener("click", async () => {
  const id = document.getElementById("editar_id").value;

  if (confirm("¿Seguro que deseas eliminar este servicio?")) {
    await fetch("../Controller/ServicioController.php", {
      method: "POST",
      body: new URLSearchParams({ accion: "eliminar", id })
    });

    modalCrud.classList.remove("activo");
    cargarServicios();
  }
});

cargarServicios();
</script>

</body>
</html>
