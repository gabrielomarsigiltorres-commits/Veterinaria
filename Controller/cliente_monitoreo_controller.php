<?php
session_start();

// --- CORRECCIÓN DEL ERROR ---
// Usamos el operador de fusión null (??) para evitar el Warning si 'rol' no existe.
// Si $_SESSION['rol'] no está definido, se usa '' (cadena vacía) y no falla.
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {
    // Si quieres redirigir a login descomenta las siguientes líneas:
    // header('Location: ../login.php');
    // exit();
}

// También protegemos el ID de usuario
$userId = $_SESSION['usuario_id'] ?? 0;

// --- ACCIONES AJAX (Para cuando la vista pide datos con JS) ---

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    // Acción: Obtener detalles de una mascota específica
    if ($_GET['action'] === 'get_pet_details') {
        $petId = $_GET['pet_id'] ?? 0;

        // AQUÍ ES DONDE DEBERÍAS CONECTAR CON TU BASE DE DATOS REAL
        // Ejemplo: $mascota = ModeloMascota::getById($petId);
        
        // --- DATOS DE PRUEBA (MOCK) ---
        // Estos datos se envían a la vista para que veas cómo queda el diseño
        $response = [
            'pet' => [
                'id' => $petId,
                'nombre' => 'Mascota Demo', // Esto debería venir de la BD
                'raza' => 'Raza Demo',
                'id_cita' => 101, // Si pones null aquí, saldrá el mensaje "Descansando en casa"
                'etapa_actual' => 2 // 0:Recepción, 1:Lavado, 2:Corte, 3:Secado, 4:Listo
            ],
            'weights' => [
                ['fecha_peso' => '2023-11-20', 'peso' => 10.5],
                ['fecha_peso' => '2023-11-29', 'peso' => 11.2]
            ],
            'vaccines' => [
                ['id' => 1, 'nombre_vacuna' => 'Rabia', 'estado' => 'completada'],
                ['id' => 2, 'nombre_vacuna' => 'Séxtuple', 'estado' => 'pendiente']
            ],
            'treatment' => [
                'active' => true, // Cambia a false para ocultar el panel rojo de tratamiento
                'medicamento' => 'Antibiótico General',
                'dosis' => '1 pastilla cada 8 horas',
                'instrucciones' => 'Dar con comida blanda.',
                'dias_progreso' => 2
            ]
        ];
        
        echo json_encode($response);
        exit;
    }
}

// --- CARGA INICIAL ---
// Datos para llenar el select "Selecciona a tu compañero" al entrar a la página
// Deberías hacer un SELECT * FROM mascotas WHERE id_usuario = $userId
$misMascotas = [
    ['id' => 1, 'nombre' => 'Terron', 'raza' => 'Gato chusco'],
    ['id' => 2, 'nombre' => 'firulaid', 'raza' => 'chusco'],
    ['id' => 3, 'nombre' => 'marco', 'raza' => 'Perro chusco']
];
?>