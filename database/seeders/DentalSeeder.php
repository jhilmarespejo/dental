<?php

// Configuración de la conexión a la base de datos
$host = 'localhost';
$dbname = 'dental';
$username = 'root';
$password = ''; // Ajusta según tu configuración

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    echo "Conexión exitosa a la base de datos.\n";
    
    // Iniciar transacción para poder revertir cambios en caso de error
    $pdo->beginTransaction();
    
    // Limpiar tablas existentes (en orden inverso por las restricciones FK)
    limpiarTablas($pdo);
    
    // Datos de prueba para cada tabla
    insertarDiagnosticos($pdo);
    insertarTratamientos($pdo);
    insertarProfesionales($pdo);
    insertarPacientes($pdo);
    vincularPacientesProfesionales($pdo);
    insertarCitas($pdo);
    insertarTratamientosRealizados($pdo);
    insertarPagos($pdo);
    insertarImagenesTratamientos($pdo);
    
    // Confirmar los cambios
    $pdo->commit();
    
    echo "Todos los datos de prueba han sido insertados correctamente.\n";
    
} catch (PDOException $e) {
    // Revertir cambios en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
}

/**
 * Limpia todas las tablas para eliminar datos existentes
 */
function limpiarTablas($pdo) {
    try {
        // Desactivar verificación de claves foráneas temporalmente
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        
        // Limpiar tablas en orden (de las que tienen más dependencias a las que tienen menos)
        $pdo->exec('TRUNCATE TABLE tratamiento_imagenes');
        $pdo->exec('TRUNCATE TABLE pagos');
        $pdo->exec('TRUNCATE TABLE tratamientos_realizados');
        $pdo->exec('TRUNCATE TABLE citas');
        $pdo->exec('TRUNCATE TABLE paciente_profesional');
        $pdo->exec('TRUNCATE TABLE pacientes');
        $pdo->exec('TRUNCATE TABLE profesionales');
        $pdo->exec('TRUNCATE TABLE tratamientos');
        $pdo->exec('TRUNCATE TABLE diagnosticos');
        
        // Restaurar verificación de claves foráneas
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        
        echo "Todas las tablas han sido limpiadas correctamente.\n";
    } catch (Exception $e) {
        echo "Error al limpiar tablas: " . $e->getMessage() . "\n";
        throw $e;
    }
}

/**
 * Inserta diagnósticos odontológicos comunes
 */
function insertarDiagnosticos($pdo) {
    $diagnosticos = [
        ['nombre' => 'Caries dental', 'descripcion' => 'Destrucción localizada de los tejidos duros del diente por acción bacteriana'],
        ['nombre' => 'Gingivitis', 'descripcion' => 'Inflamación de las encías por acumulación de placa bacteriana'],
        ['nombre' => 'Periodontitis', 'descripcion' => 'Inflamación e infección que destruye los tejidos de soporte de los dientes'],
        ['nombre' => 'Pulpitis', 'descripcion' => 'Inflamación de la pulpa dental que puede ser reversible o irreversible'],
        ['nombre' => 'Absceso dental', 'descripcion' => 'Acumulación de pus causada por una infección bacteriana'],
        ['nombre' => 'Fractura dental', 'descripcion' => 'Ruptura parcial o completa de un diente'],
        ['nombre' => 'Bruxismo', 'descripcion' => 'Hábito involuntario de apretar o rechinar los dientes'],
        ['nombre' => 'Maloclusión', 'descripcion' => 'Desalineación dental que afecta la mordida'],
        ['nombre' => 'Impactación', 'descripcion' => 'Diente que no ha erupcionado completamente o está bloqueado'],
        ['nombre' => 'Hipersensibilidad dental', 'descripcion' => 'Dolor agudo y temporal en respuesta a estímulos térmicos, táctiles u osmóticos'],
        ['nombre' => 'Fluorosis dental', 'descripcion' => 'Hipomineralización del esmalte por exceso de flúor durante el desarrollo dental'],
        ['nombre' => 'Estomatitis', 'descripcion' => 'Inflamación de la mucosa oral'],
        ['nombre' => 'Halitosis', 'descripcion' => 'Mal aliento causado por problemas bucales, digestivos o respiratorios'],
        ['nombre' => 'Candidiasis oral', 'descripcion' => 'Infección fúngica de la boca causada por Candida albicans'],
        ['nombre' => 'Erosión dental', 'descripcion' => 'Pérdida de tejido dental duro por procesos químicos no bacterianos']
    ];

    $stmt = $pdo->prepare("INSERT INTO diagnosticos (nombre, descripcion) VALUES (:nombre, :descripcion)");
    
    foreach ($diagnosticos as $diagnostico) {
        $stmt->execute([
            ':nombre' => $diagnostico['nombre'],
            ':descripcion' => $diagnostico['descripcion']
        ]);
    }
    
    echo "Insertados " . count($diagnosticos) . " diagnósticos\n";
}

/**
 * Inserta tratamientos odontológicos comunes con precios en Bolivianos (Bs)
 */
function insertarTratamientos($pdo) {
    $tratamientos = [
        ['nombre' => 'Limpieza dental', 'descripcion' => 'Eliminación de placa bacteriana y sarro', 'costo_sugerido' => 200.00],
        ['nombre' => 'Obturación (empaste) simple', 'descripcion' => 'Restauración de diente con caries', 'costo_sugerido' => 150.00],
        ['nombre' => 'Obturación (empaste) compuesta', 'descripcion' => 'Restauración compleja de diente con caries', 'costo_sugerido' => 250.00],
        ['nombre' => 'Tratamiento de conducto', 'descripcion' => 'Endodoncia para tratar infección o inflamación de la pulpa dental', 'costo_sugerido' => 800.00],
        ['nombre' => 'Extracción dental simple', 'descripcion' => 'Remoción de diente visible', 'costo_sugerido' => 180.00],
        ['nombre' => 'Extracción de muela del juicio', 'descripcion' => 'Remoción de terceros molares', 'costo_sugerido' => 500.00],
        ['nombre' => 'Corona dental', 'descripcion' => 'Restauración que cubre toda la superficie del diente', 'costo_sugerido' => 1000.00],
        ['nombre' => 'Puente dental', 'descripcion' => 'Prótesis fija para reemplazar dientes ausentes', 'costo_sugerido' => 2500.00],
        ['nombre' => 'Implante dental', 'descripcion' => 'Reemplazo permanente de raíz dental', 'costo_sugerido' => 3500.00],
        ['nombre' => 'Prótesis dental removible', 'descripcion' => 'Reemplazo artificial de dientes que se puede quitar', 'costo_sugerido' => 1800.00],
        ['nombre' => 'Blanqueamiento dental', 'descripcion' => 'Tratamiento estético para aclarar el color de los dientes', 'costo_sugerido' => 1200.00],
        ['nombre' => 'Aplicación de flúor', 'descripcion' => 'Tratamiento preventivo para fortalecer el esmalte', 'costo_sugerido' => 120.00],
        ['nombre' => 'Selladores dentales', 'descripcion' => 'Protección para prevenir caries en surcos y fisuras', 'costo_sugerido' => 100.00],
        ['nombre' => 'Tratamiento de gingivitis', 'descripcion' => 'Manejo de inflamación gingival', 'costo_sugerido' => 300.00],
        ['nombre' => 'Tratamiento periodontal', 'descripcion' => 'Limpieza profunda para tratar enfermedad periodontal', 'costo_sugerido' => 600.00],
        ['nombre' => 'Ortodoncia (brackets)', 'descripcion' => 'Tratamiento para corregir maloclusiones con aparatos fijos', 'costo_sugerido' => 4000.00],
        ['nombre' => 'Radiografía dental', 'descripcion' => 'Imagen diagnóstica de dientes y estructuras circundantes', 'costo_sugerido' => 80.00],
        ['nombre' => 'Reconstrucción dental', 'descripcion' => 'Restauración extensa de diente dañado', 'costo_sugerido' => 350.00],
        ['nombre' => 'Férula de descarga', 'descripcion' => 'Dispositivo para tratar bruxismo y trastornos temporomandibulares', 'costo_sugerido' => 500.00],
        ['nombre' => 'Carillas dentales', 'descripcion' => 'Láminas delgadas que se adhieren a la superficie frontal de los dientes', 'costo_sugerido' => 800.00]
    ];

    $stmt = $pdo->prepare("INSERT INTO tratamientos (nombre, descripcion, costo_sugerido) VALUES (:nombre, :descripcion, :costo_sugerido)");
    
    foreach ($tratamientos as $tratamiento) {
        $stmt->execute([
            ':nombre' => $tratamiento['nombre'],
            ':descripcion' => $tratamiento['descripcion'],
            ':costo_sugerido' => $tratamiento['costo_sugerido']
        ]);
    }
    
    echo "Insertados " . count($tratamientos) . " tratamientos\n";
}

/**
 * Inserta profesionales odontólogos con nombres bolivianos
 */
function insertarProfesionales($pdo) {
    $profesionales = [
        [
            'nombres' => 'Carlos Alberto',
            'apellidos' => 'Mamani Quispe',
            'especialidad' => 'Odontología General',
            'email' => 'carlos.mamani@dentalclinic.bo',
            'telefono' => '71234567',
            'ci' => '4587412',
            'ci_exp' => 'LP',
            'estado' => 'activo'
        ],
        [
            'nombres' => 'María Fernanda',
            'apellidos' => 'Flores Condori',
            'especialidad' => 'Ortodoncia',
            'email' => 'maria.flores@dentalclinic.bo',
            'telefono' => '72345678',
            'ci' => '5487123',
            'ci_exp' => 'LP',
            'estado' => 'activo'
        ],
        [
            'nombres' => 'José Luis',
            'apellidos' => 'Choque Huanca',
            'especialidad' => 'Cirugía Oral',
            'email' => 'jose.choque@dentalclinic.bo',
            'telefono' => '73456789',
            'ci' => '3124578',
            'ci_exp' => 'SC',
            'estado' => 'activo'
        ],
        [
            'nombres' => 'Ana Patricia',
            'apellidos' => 'Vargas Torrez',
            'especialidad' => 'Endodoncia',
            'email' => 'ana.vargas@dentalclinic.bo',
            'telefono' => '74567890',
            'ci' => '6547123',
            'ci_exp' => 'CB',
            'estado' => 'activo'
        ],
        [
            'nombres' => 'Roberto Carlos',
            'apellidos' => 'Mendoza Vaca',
            'especialidad' => 'Periodoncia',
            'email' => 'roberto.mendoza@dentalclinic.bo',
            'telefono' => '75678901',
            'ci' => '4125789',
            'ci_exp' => 'SC',
            'estado' => 'activo'
        ],
        [
            'nombres' => 'Laura Daniela',
            'apellidos' => 'Gutiérrez Álvarez',
            'especialidad' => 'Odontopediatría',
            'email' => 'laura.gutierrez@dentalclinic.bo',
            'telefono' => '76789012',
            'ci' => '5214789',
            'ci_exp' => 'LP',
            'estado' => 'activo'
        ],
        [
            'nombres' => 'Miguel Ángel',
            'apellidos' => 'Ticona Morales',
            'especialidad' => 'Prostodoncia',
            'email' => 'miguel.ticona@dentalclinic.bo',
            'telefono' => '77890123',
            'ci' => '4789512',
            'ci_exp' => 'OR',
            'estado' => 'activo'
        ],
        [
            'nombres' => 'Patricia Andrea',
            'apellidos' => 'Cruz Lima',
            'especialidad' => 'Odontología Estética',
            'email' => 'patricia.cruz@dentalclinic.bo',
            'telefono' => '78901234',
            'ci' => '3548791',
            'ci_exp' => 'PT',
            'estado' => 'vacaciones'
        ],
        [
            'nombres' => 'Juan Fernando',
            'apellidos' => 'Tapia Calle',
            'especialidad' => 'Radiología Oral',
            'email' => 'juan.tapia@dentalclinic.bo',
            'telefono' => '79012345',
            'ci' => '6321547',
            'ci_exp' => 'TJ',
            'estado' => 'inactivo'
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO profesionales (nombres, apellidos, especialidad, email, telefono, ci, ci_exp, estado) 
        VALUES (:nombres, :apellidos, :especialidad, :email, :telefono, :ci, :ci_exp, :estado)
    ");
    
    foreach ($profesionales as $profesional) {
        $stmt->execute([
            ':nombres' => $profesional['nombres'],
            ':apellidos' => $profesional['apellidos'],
            ':especialidad' => $profesional['especialidad'],
            ':email' => $profesional['email'],
            ':telefono' => $profesional['telefono'],
            ':ci' => $profesional['ci'],
            ':ci_exp' => $profesional['ci_exp'],
            ':estado' => $profesional['estado']
        ]);
    }
    
    echo "Insertados " . count($profesionales) . " profesionales\n";
}

/**
 * Inserta pacientes con nombres bolivianos y direcciones locales
 */
function insertarPacientes($pdo) {
    // Apellidos comunes bolivianos
    $apellidos = [
        'Mamani', 'Quispe', 'Condori', 'Choque', 'Flores', 'Huanca', 'Vargas', 'López', 
        'Torrez', 'Gutiérrez', 'Rodríguez', 'Fernández', 'Mendoza', 'Vaca', 'Rojas', 
        'Cruz', 'Tapia', 'Morales', 'Calle', 'Álvarez', 'Lima', 'Ticona', 'Colque'
    ];
    
    // Nombres masculinos comunes
    $nombresMasculinos = [
        'Carlos', 'José', 'Juan', 'Luis', 'Miguel', 'Roberto', 'Fernando', 'Mario', 
        'Pedro', 'Daniel', 'Álvaro', 'Jorge', 'Marcelo', 'David', 'Gonzalo', 'Rodrigo', 'René'
    ];
    
    // Nombres femeninos comunes
    $nombresFemeninos = [
        'María', 'Ana', 'Patricia', 'Claudia', 'Laura', 'Carmen', 'Sandra', 'Mónica', 
        'Verónica', 'Isabel', 'Carla', 'Silvia', 'Martha', 'Roxana', 'Andrea', 'Cecilia', 'Gabriela'
    ];
    
    // Zonas y barrios de La Paz, Santa Cruz y Cochabamba
    $zonas = [
        // La Paz
        'Zona Sur', 'Sopocachi', 'Miraflores', 'San Pedro', 'Calacoto', 'Obrajes', 
        'Achumani', 'Villa Fátima', 'Cota Cota', 'Los Pinos', 'Irpavi', 'El Alto',
        
        // Santa Cruz
        'Equipetrol', 'Plan 3000', 'Villa 1ro de Mayo', 'Urbarí', 'Las Palmas', 
        'Los Pozos', 'Barrio Sirari', 'La Ramada', 'Barrio Hamacas',
        
        // Cochabamba
        'La Recoleta', 'Cala Cala', 'Queru Queru', 'Sarco', 'La Cancha', 
        'América Este', 'Tupuraya', 'Las Cuadras', 'Mayorazgo'
    ];
    
    // Calles comunes bolivianas
    $calles = [
        'Av. 16 de Julio', 'Calle Illampu', 'Av. 6 de Agosto', 'Calle Comercio', 
        'Av. Camacho', 'Calle Murillo', 'Av. Arce', 'Calle Loayza', 'Av. Busch', 
        'Calle Sagárnaga', 'Av. Sánchez Lima', 'Calle Potosí', 'Av. Montes', 
        'Calle Sucre', 'Av. Illimani', 'Calle Colón', 'Av. Buenos Aires'
    ];
    
    // Alergias comunes
    $alergias = [
        'Amoxicilina', 'Penicilina', 'Ibuprofeno', 'Paracetamol', 'Aspirina', 
        'Látex', 'Ninguna conocida', 'Lidocaína', 'Anestésicos locales'
    ];
    
    // Condiciones médicas
    $condicionesMedicas = [
        'Hipertensión', 'Diabetes tipo 2', 'Asma', 'Ninguna', 'Hipotiroidismo', 
        'Hipertiroidismo', 'Artritis', 'Colesterol alto', 'Enfermedad cardíaca'
    ];
    
    // Generar 50 pacientes
    $pacientes = [];
    for ($i = 0; $i < 50; $i++) {
        $genero = ($i % 2 == 0) ? 'M' : 'F';
        $nombres = ($genero == 'M') 
            ? $nombresMasculinos[array_rand($nombresMasculinos)] . ' ' . $nombresMasculinos[array_rand($nombresMasculinos)] 
            : $nombresFemeninos[array_rand($nombresFemeninos)] . ' ' . $nombresFemeninos[array_rand($nombresFemeninos)];
        
        // Generar dos apellidos aleatorios
        $apellido1 = $apellidos[array_rand($apellidos)];
        $apellido2 = $apellidos[array_rand($apellidos)];
        
        // Evitar que ambos apellidos sean iguales
        while ($apellido1 == $apellido2) {
            $apellido2 = $apellidos[array_rand($apellidos)];
        }
        
        // Generar fecha de nacimiento entre 10 y 80 años atrás
        $anioNacimiento = rand(date('Y') - 80, date('Y') - 10);
        $mesNacimiento = rand(1, 12);
        $diaNacimiento = rand(1, 28); // Para evitar problemas con meses de menos días
        $fechaNacimiento = sprintf('%04d-%02d-%02d', $anioNacimiento, $mesNacimiento, $diaNacimiento);
        
        // Generar CI único (número de 7 dígitos)
        $ci = rand(1000000, 9999999);
        
        // Departamentos bolivianos
        $departamentos = ['LP', 'SC', 'CB', 'OR', 'PT', 'TJ', 'CH', 'BN', 'PD'];
        $ciExp = $departamentos[array_rand($departamentos)];
        
        // Generar dirección
        $direccion = $calles[array_rand($calles)] . ' #' . rand(100, 9999) . ', ' . $zonas[array_rand($zonas)];
        
        // Generar email usando el primer nombre y primer apellido
        $primerNombre = strtolower(explode(' ', $nombres)[0]);
        $email = $primerNombre . '.' . strtolower($apellido1) . '@gmail.com';
        
        // Generar número de celular boliviano (7 dígitos con prefijo 7)
        $celular = '7' . rand(1000000, 9999999);
        
        // Generar alergias y condiciones médicas (algunas en blanco)
        $tieneAlergias = rand(0, 100) > 70; // 30% tiene alergias
        $pacienteAlergias = $tieneAlergias ? $alergias[array_rand($alergias)] : null;
        
        $tieneCondiciones = rand(0, 100) > 60; // 40% tiene condiciones médicas
        $pacienteCondiciones = $tieneCondiciones ? $condicionesMedicas[array_rand($condicionesMedicas)] : null;
        
        // Última visita (algunos sin visita previa)
        $tieneVisitaPrevia = rand(0, 100) > 20; // 80% tiene visita previa
        if ($tieneVisitaPrevia) {
            $ultimaVisitaOffset = rand(1, 365); // Última visita entre 1 y 365 días atrás
            $ultimaVisita = date('Y-m-d', strtotime("-$ultimaVisitaOffset days"));
        } else {
            $ultimaVisita = null;
        }
        
        $pacientes[] = [
            'nombres' => $nombres,
            'apellidos' => $apellido1 . ' ' . $apellido2,
            'fecha_nacimiento' => $fechaNacimiento,
            'genero' => $genero,
            'celular' => $celular,
            'email' => $email,
            'ci' => $ci,
            'ci_exp' => $ciExp,
            'direccion' => $direccion,
            'alergias' => $pacienteAlergias,
            'condiciones_medicas' => $pacienteCondiciones,
            'fecha_ultima_visita' => $ultimaVisita
        ];
    }

    $stmt = $pdo->prepare("
        INSERT INTO pacientes (nombres, apellidos, fecha_nacimiento, genero, celular, email, ci, ci_exp, direccion, alergias, condiciones_medicas, fecha_ultima_visita) 
        VALUES (:nombres, :apellidos, :fecha_nacimiento, :genero, :celular, :email, :ci, :ci_exp, :direccion, :alergias, :condiciones_medicas, :fecha_ultima_visita)
    ");
    
    foreach ($pacientes as $paciente) {
        $stmt->execute([
            ':nombres' => $paciente['nombres'],
            ':apellidos' => $paciente['apellidos'],
            ':fecha_nacimiento' => $paciente['fecha_nacimiento'],
            ':genero' => $paciente['genero'],
            ':celular' => $paciente['celular'],
            ':email' => $paciente['email'],
            ':ci' => $paciente['ci'],
            ':ci_exp' => $paciente['ci_exp'],
            ':direccion' => $paciente['direccion'],
            ':alergias' => $paciente['alergias'],
            ':condiciones_medicas' => $paciente['condiciones_medicas'],
            ':fecha_ultima_visita' => $paciente['fecha_ultima_visita']
        ]);
    }
    
    echo "Insertados " . count($pacientes) . " pacientes\n";
}

/**
 * Vincula pacientes con profesionales
 */
function vincularPacientesProfesionales($pdo) {
    // Obtener todos los pacientes y profesionales
    $pacientes = $pdo->query("SELECT id FROM pacientes")->fetchAll();
    $profesionales = $pdo->query("SELECT id FROM profesionales WHERE estado = 'activo'")->fetchAll();
    
    // Posibles notas para la asignación
    $notas = [
        'Paciente solicita ser atendido por este profesional',
        'Asignado por recomendación',
        'Asignación temporal',
        'Paciente requiere atención especializada',
        'Transferido desde otro profesional',
        null, // Algunos sin notas
        null,
        null
    ];
    
    // Cada paciente será asignado a 1-3 profesionales
    $asignaciones = [];
    
    foreach ($pacientes as $paciente) {
        // Seleccionar 1-3 profesionales aleatorios para cada paciente
        $numProfesionales = rand(1, 3);
        $profesionalesAsignados = array_rand($profesionales, min($numProfesionales, count($profesionales)));
        
        // Convertir a array si solo es un profesional
        if (!is_array($profesionalesAsignados)) {
            $profesionalesAsignados = [$profesionalesAsignados];
        }
        
        foreach ($profesionalesAsignados as $key) {
            $profesional = $profesionales[$key];
            
            // Fecha de asignación entre 1 y 365 días atrás
            $diasAtras = rand(1, 365);
            $fechaAsignacion = date('Y-m-d', strtotime("-$diasAtras days"));
            
            $asignaciones[] = [
                'paciente_id' => $paciente['id'],
                'profesional_id' => $profesional['id'],
                'fecha_asignacion' => $fechaAsignacion,
                'notas' => $notas[array_rand($notas)]
            ];
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO paciente_profesional (paciente_id, profesional_id, fecha_asignacion, notas) 
        VALUES (:paciente_id, :profesional_id, :fecha_asignacion, :notas)
    ");
    
    foreach ($asignaciones as $asignacion) {
        $stmt->execute([
            ':paciente_id' => $asignacion['paciente_id'],
            ':profesional_id' => $asignacion['profesional_id'],
            ':fecha_asignacion' => $asignacion['fecha_asignacion'],
            ':notas' => $asignacion['notas']
        ]);
    }
    
    echo "Insertadas " . count($asignaciones) . " asignaciones de paciente-profesional\n";
}

/**
 * Inserta citas dentales
 */
function insertarCitas($pdo) {
    // Obtener relaciones paciente-profesional
    $relaciones = $pdo->query("
        SELECT paciente_id, profesional_id FROM paciente_profesional
    ")->fetchAll();
    
    // Posibles estados de citas
    $estados = ['programada', 'confirmada', 'completada', 'cancelada'];
    
    // Probabilidades para cada estado:
    // - 20% programada
    // - 20% confirmada
    // - 50% completada
    // - 10% cancelada
    $pesos = [20, 20, 50, 10];
    
    // Posibles motivos de cita
    $motivos = [
        'Revisión general',
        'Limpieza dental',
        'Dolor de muelas',
        'Extracción dental',
        'Tratamiento de caries',
        'Blanqueamiento dental',
        'Consulta ortodoncia',
        'Empaste dental',
        'Radiografía',
        'Revisión de tratamiento',
        'Urgencia dental',
        'Control periódico'
    ];
    
    // Posibles notas
    $notas = [
        'Paciente solicita anestesia local',
        'Paciente ansioso, requiere atención delicada',
        'Alergias revisadas en historial',
        'Primera visita',
        'Control de tratamiento previo',
        'Traer radiografías previas',
        'Paciente no debe consumir alimentos sólidos 2 horas antes',
        null, // Algunos sin notas
        null,
        null
    ];
    
    // Generar 200 citas
    $citas = [];
    
    for ($i = 0; $i < 200; $i++) {
        // Seleccionar una relación paciente-profesional aleatoria
        $relacion = $relaciones[array_rand($relaciones)];
        
        // Generar fecha y hora de cita (entre 90 días atrás y 30 días adelante)
        $offsetDias = rand(-90, 30);
        $fechaBase = strtotime("$offsetDias days");
        
        // Horario de atención: 8:00 a 19:00, solo días laborables (lunes a viernes)
        while (date('N', $fechaBase) >= 6) { // 6 = sábado, 7 = domingo
            $fechaBase = strtotime("+1 day", $fechaBase);
        }
        
        // Hora aleatoria en intervalos de 30 minutos
        $horas = range(8, 18); // 8:00 AM a 6:30 PM (última cita)
        $minutos = [0, 30];
        
        $hora = $horas[array_rand($horas)];
        $minuto = $minutos[array_rand($minutos)];
        
        $fechaHora = date('Y-m-d H:i:s', strtotime(date('Y-m-d', $fechaBase) . " $hora:$minuto:00"));
        
        // Duración aleatoria (30, 45 o 60 minutos)
        $duraciones = [30, 45, 60];
        $duracion = $duraciones[array_rand($duraciones)];
        
        // Estado de la cita (basado en las probabilidades)
        $estadoIndex = estadoPonderado($pesos);
        $estado = $estados[$estadoIndex];
        
        // Si la fecha es futura, no puede estar completada
        if ($offsetDias > 0 && $estado == 'completada') {
            $estado = 'programada';
        }
        
        // Si la fecha es pasada, no puede estar programada o confirmada
        if ($offsetDias < 0 && ($estado == 'programada' || $estado == 'confirmada')) {
            $estado = 'completada';
        }
        
        $citas[] = [
            'paciente_id' => $relacion['paciente_id'],
            'profesional_id' => $relacion['profesional_id'],
            'fecha_hora' => $fechaHora,
            'duracion' => $duracion,
            'estado' => $estado,
            'motivo' => $motivos[array_rand($motivos)],
            'notas' => rand(0, 1) ? $notas[array_rand($notas)] : null // 50% con notas
        ];
    }

    $stmt = $pdo->prepare("
        INSERT INTO citas (paciente_id, profesional_id, fecha_hora, duracion, estado, motivo, notas) 
        VALUES (:paciente_id, :profesional_id, :fecha_hora, :duracion, :estado, :motivo, :notas)
    ");
    
    foreach ($citas as $cita) {
        $stmt->execute([
            ':paciente_id' => $cita['paciente_id'],
            ':profesional_id' => $cita['profesional_id'],
            ':fecha_hora' => $cita['fecha_hora'],
            ':duracion' => $cita['duracion'],
            ':estado' => $cita['estado'],
            ':motivo' => $cita['motivo'],
            ':notas' => $cita['notas']
        ]);
    }
    
    echo "Insertadas " . count($citas) . " citas\n";
}

/**
 * Función auxiliar para seleccionar un estado según pesos
 */
function estadoPonderado($pesos) {
    $total = array_sum($pesos);
    $aleatorio = rand(1, $total);
    
    $acumulado = 0;
    foreach ($pesos as $indice => $peso) {
        $acumulado += $peso;
        if ($aleatorio <= $acumulado) {
            return $indice;
        }
    }
    
    return 0; // Por defecto, devuelve el primer estado
}

/**
 * Inserta tratamientos realizados
 */
function insertarTratamientosRealizados($pdo) {
    // Obtener citas completadas
    $citas = $pdo->query("
        SELECT id, paciente_id, profesional_id, fecha_hora 
        FROM citas 
        WHERE estado = 'completada'
    ")->fetchAll();
    
    // Obtener todos los diagnósticos y tratamientos
    $diagnosticos = $pdo->query("SELECT id, nombre FROM diagnosticos")->fetchAll();
    $tratamientos = $pdo->query("SELECT id, nombre, costo_sugerido FROM tratamientos")->fetchAll();
    
    // Posibles piezas dentales
    $piezasDentales = [];
    
    // Incisivos, caninos, premolares y molares superiores (cuadrantes 1 y 2)
    for ($i = 11; $i <= 18; $i++) $piezasDentales[] = $i;
    for ($i = 21; $i <= 28; $i++) $piezasDentales[] = $i;
    
    // Incisivos, caninos, premolares y molares inferiores (cuadrantes 3 y 4)
    for ($i = 31; $i <= 38; $i++) $piezasDentales[] = $i;
    for ($i = 41; $i <= 48; $i++) $piezasDentales[] = $i;
    
    // Posibles observaciones
    $observaciones = [
        'Tratamiento completado satisfactoriamente',
        'Requiere seguimiento en próxima cita',
        'Paciente reporta molestia durante el procedimiento',
        'Se recomienda control en 6 meses',
        'Tratamiento parcial, pendiente segunda fase',
        'Se prescribió antibiótico post-tratamiento',
        'Se observa inflamación moderada, se recomienda aplicar frío',
        'Complicaciones menores durante el procedimiento',
        null, // Algunos sin observaciones
        null
    ];
    
    $tratamientosRealizados = [];
    
    // Generar tratamientos para cada cita completada
    foreach ($citas as $cita) {
        // Probabilidad de tener un tratamiento registrado (90%)
        if (rand(1, 100) <= 90) {
            // Fecha del tratamiento (misma fecha que la cita)
            $fechaTratamiento = date('Y-m-d', strtotime($cita['fecha_hora']));
            
            // Algunos tratamientos no tienen diagnóstico o tratamiento registrado en el catálogo
            $usarDiagnosticoCatalogo = rand(1, 100) <= 85; // 85% usa catálogo
            $usarTratamientoCatalogo = rand(1, 100) <= 90; // 90% usa catálogo
            
            // Seleccionar diagnóstico
            $diagnosticoId = null;
            $diagnosticoOtro = null;
            
            if ($usarDiagnosticoCatalogo) {
                $diagnostico = $diagnosticos[array_rand($diagnosticos)];
                $diagnosticoId = $diagnostico['id'];
            } else {
                $diagnosticosNoRegistrados = [
                    'Sensibilidad en incisivo lateral',
                    'Dolor agudo post-traumático',
                    'Problema de oclusión no clasificado',
                    'Mancha dental atípica',
                    'Desgaste prematuro de restauración'
                ];
                $diagnosticoOtro = $diagnosticosNoRegistrados[array_rand($diagnosticosNoRegistrados)];
            }
            
            // Seleccionar tratamiento
            $tratamientoId = null;
            $tratamientoOtro = null;
            $costo = 0;
            
            if ($usarTratamientoCatalogo) {
                $tratamiento = $tratamientos[array_rand($tratamientos)];
                $tratamientoId = $tratamiento['id'];
                
                // Costo: costo sugerido ±15%
                $variacion = $tratamiento['costo_sugerido'] * (rand(-15, 15) / 100);
                $costo = round($tratamiento['costo_sugerido'] + $variacion, 2);
            } else {
                $tratamientosNoRegistrados = [
                    'Aplicación de ionómero especial',
                    'Ajuste oclusal puntual',
                    'Microabrasión localizada',
                    'Tratamiento experimental con láser',
                    'Técnica mixta de restauración'
                ];
                $tratamientoOtro = $tratamientosNoRegistrados[array_rand($tratamientosNoRegistrados)];
                
                // Costo para tratamientos no catalogados (entre 100 y 1000 Bs)
                $costo = rand(100, 1000);
            }
            
            // Algunos tratamientos no especifican pieza dental
            $piezaDental = rand(1, 100) <= 70 ? strval($piezasDentales[array_rand($piezasDentales)]) : null;
            
            $tratamientosRealizados[] = [
                'paciente_id' => $cita['paciente_id'],
                'profesional_id' => $cita['profesional_id'],
                'cita_id' => $cita['id'],
                'fecha' => $fechaTratamiento,
                'diagnostico_id' => $diagnosticoId,
                'diagnostico_otro' => $diagnosticoOtro,
                'tratamiento_id' => $tratamientoId,
                'tratamiento_otro' => $tratamientoOtro,
                'pieza_dental' => $piezaDental,
                'costo' => $costo,
                'observaciones' => $observaciones[array_rand($observaciones)]
            ];
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO tratamientos_realizados 
        (paciente_id, profesional_id, cita_id, fecha, diagnostico_id, diagnostico_otro, 
        tratamiento_id, tratamiento_otro, pieza_dental, costo, observaciones) 
        VALUES 
        (:paciente_id, :profesional_id, :cita_id, :fecha, :diagnostico_id, :diagnostico_otro, 
        :tratamiento_id, :tratamiento_otro, :pieza_dental, :costo, :observaciones)
    ");
    
    foreach ($tratamientosRealizados as $tratamiento) {
        $stmt->execute([
            ':paciente_id' => $tratamiento['paciente_id'],
            ':profesional_id' => $tratamiento['profesional_id'],
            ':cita_id' => $tratamiento['cita_id'],
            ':fecha' => $tratamiento['fecha'],
            ':diagnostico_id' => $tratamiento['diagnostico_id'],
            ':diagnostico_otro' => $tratamiento['diagnostico_otro'],
            ':tratamiento_id' => $tratamiento['tratamiento_id'],
            ':tratamiento_otro' => $tratamiento['tratamiento_otro'],
            ':pieza_dental' => $tratamiento['pieza_dental'],
            ':costo' => $tratamiento['costo'],
            ':observaciones' => $tratamiento['observaciones']
        ]);
    }
    
    echo "Insertados " . count($tratamientosRealizados) . " tratamientos realizados\n";
}

/**
 * Inserta pagos de tratamientos
 */
function insertarPagos($pdo) {
    // Obtener todos los tratamientos realizados
    $tratamientos = $pdo->query("SELECT id, fecha, costo FROM tratamientos_realizados")->fetchAll();
    
    // Métodos de pago
    $metodosPago = ['efectivo', 'tarjeta', 'transferencia', 'otro'];
    
    // Pesos para métodos de pago (en Bolivia efectivo es más común)
    $pesosPagos = [70, 15, 10, 5]; // 70% efectivo, 15% tarjeta, 10% transferencia, 5% otro
    
    // Posibles notas de pago
    $notas = [
        'Pago completo',
        'Pago a cuenta',
        'Pago con descuento por convenio',
        'Pago realizado por familiar',
        'Pago con billete de 200 Bs',
        'Transferencia desde Banco BNB',
        'Pago con tarjeta de crédito',
        'Pago en cuotas',
        null, // Algunos sin notas
        null
    ];
    
    $pagos = [];
    
    foreach ($tratamientos as $tratamiento) {
        $montoPagado = 0;
        $costoPendiente = $tratamiento['costo'];
        
        // Algunos tratamientos tienen pagos parciales o múltiples
        $tipoPago = rand(1, 100);
        
        if ($tipoPago <= 70) {
            // 70% de los tratamientos se pagan completamente en un solo pago
            $pagos[] = [
                'tratamiento_id' => $tratamiento['id'],
                'fecha' => $tratamiento['fecha'], // Pago el mismo día
                'monto' => $costoPendiente,
                'metodo_pago' => metodoPagoPonderado($metodosPago, $pesosPagos),
                'comprobante' => 'REC-' . rand(1000, 9999),
                'notas' => $notas[array_rand($notas)]
            ];
            
            $montoPagado = $costoPendiente;
            
        } elseif ($tipoPago <= 90) {
            // 20% se paga en 2 partes
            $primerPago = round($costoPendiente * (rand(30, 70) / 100), 2); // Entre 30% y 70% del total
            $segundoPago = $costoPendiente - $primerPago;
            
            // Primer pago (día del tratamiento)
            $pagos[] = [
                'tratamiento_id' => $tratamiento['id'],
                'fecha' => $tratamiento['fecha'],
                'monto' => $primerPago,
                'metodo_pago' => metodoPagoPonderado($metodosPago, $pesosPagos),
                'comprobante' => 'REC-' . rand(1000, 9999),
                'notas' => 'Primer pago'
            ];
            
            // Segundo pago (1-30 días después)
            $diasDespues = rand(1, 30);
            $fechaSegundoPago = date('Y-m-d', strtotime($tratamiento['fecha'] . " +$diasDespues days"));
            
            $pagos[] = [
                'tratamiento_id' => $tratamiento['id'],
                'fecha' => $fechaSegundoPago,
                'monto' => $segundoPago,
                'metodo_pago' => metodoPagoPonderado($metodosPago, $pesosPagos),
                'comprobante' => 'REC-' . rand(1000, 9999),
                'notas' => 'Segundo pago (saldo)'
            ];
            
            $montoPagado = $costoPendiente;
            
        } else {
            // 10% se paga parcialmente (o queda sin pagar)
            $montoParcial = round($costoPendiente * (rand(40, 90) / 100), 2); // Entre 40% y 90% del total
            
            $pagos[] = [
                'tratamiento_id' => $tratamiento['id'],
                'fecha' => $tratamiento['fecha'],
                'monto' => $montoParcial,
                'metodo_pago' => metodoPagoPonderado($metodosPago, $pesosPagos),
                'comprobante' => 'REC-' . rand(1000, 9999),
                'notas' => 'Pago parcial - Saldo pendiente'
            ];
            
            $montoPagado = $montoParcial;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO pagos (tratamiento_id, fecha, monto, metodo_pago, comprobante, notas) 
        VALUES (:tratamiento_id, :fecha, :monto, :metodo_pago, :comprobante, :notas)
    ");
    
    foreach ($pagos as $pago) {
        $stmt->execute([
            ':tratamiento_id' => $pago['tratamiento_id'],
            ':fecha' => $pago['fecha'],
            ':monto' => $pago['monto'],
            ':metodo_pago' => $pago['metodo_pago'],
            ':comprobante' => $pago['comprobante'],
            ':notas' => $pago['notas']
        ]);
    }
    
    echo "Insertados " . count($pagos) . " pagos\n";
}

/**
 * Función auxiliar para seleccionar método de pago según pesos
 */
function metodoPagoPonderado($metodos, $pesos) {
    $indice = estadoPonderado($pesos);
    return $metodos[$indice];
}

/**
 * Inserta imágenes asociadas a tratamientos
 */
function insertarImagenesTratamientos($pdo) {
    // Obtener tratamientos realizados
    $tratamientos = $pdo->query("SELECT id FROM tratamientos_realizados")->fetchAll();
    
    // Tipos de archivos de imagen dental
    $tiposArchivo = ['image/jpeg', 'image/png', 'application/pdf', 'image/dicom'];
    
    // Nombres de archivos por tipo
    $nombresArchivo = [
        'image/jpeg' => ['radiografia', 'foto_clinica', 'panoramica', 'oclusal', 'preoperatorio', 'postoperatorio'],
        'image/png' => ['captura_escaner', 'modelo_3d', 'tomografia', 'mordida'],
        'application/pdf' => ['informe', 'historial', 'estudio', 'presupuesto'],
        'image/dicom' => ['tac_dental', 'cefalometria', 'ortopantomografia']
    ];
    
    // Descripciones para imágenes
    $descripciones = [
        'Radiografía periapical',
        'Fotografía pre-tratamiento',
        'Fotografía post-tratamiento',
        'Radiografía panorámica',
        'Imagen de diagnóstico',
        'Captura de escáner intraoral',
        'Modelo 3D para planificación',
        'Imagen de seguimiento',
        'Documentación clínica',
        'Informe radiológico'
    ];
    
    $imagenes = [];
    
    // Generar imágenes para aproximadamente 60% de los tratamientos
    $tratamientosConImagenes = array_rand($tratamientos, ceil(count($tratamientos) * 0.6));
    
    if (!is_array($tratamientosConImagenes)) {
        $tratamientosConImagenes = [$tratamientosConImagenes];
    }
    
    foreach ($tratamientosConImagenes as $key) {
        $tratamiento = $tratamientos[$key];
        
        // Cada tratamiento tiene 1-4 imágenes
        $numImagenes = rand(1, 4);
        
        for ($i = 0; $i < $numImagenes; $i++) {
            // Seleccionar tipo de archivo
            $tipoArchivo = $tiposArchivo[array_rand($tiposArchivo)];
            
            // Seleccionar nombre base según el tipo
            $nombreBase = $nombresArchivo[$tipoArchivo][array_rand($nombresArchivo[$tipoArchivo])];
            
            // Crear nombre de archivo
            $nombreArchivo = $nombreBase . '_' . $tratamiento['id'] . '_' . ($i + 1) . obtenerExtension($tipoArchivo);
            
            // Ruta de archivo (simulada)
            $rutaArchivo = 'imagenes/tratamientos/' . date('Y/m') . '/' . $nombreArchivo;
            
            // Tamaño en bytes (entre 100KB y 5MB)
            $tamano = rand(100 * 1024, 5 * 1024 * 1024);
            
            $imagenes[] = [
                'tratamiento_id' => $tratamiento['id'],
                'ruta_archivo' => $rutaArchivo,
                'nombre_archivo' => $nombreArchivo,
                'tipo_archivo' => $tipoArchivo,
                'descripcion' => $descripciones[array_rand($descripciones)],
                'tamano' => $tamano,
                'orden' => $i + 1
            ];
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO tratamiento_imagenes 
        (tratamiento_id, ruta_archivo, nombre_archivo, tipo_archivo, descripcion, tamano, orden) 
        VALUES 
        (:tratamiento_id, :ruta_archivo, :nombre_archivo, :tipo_archivo, :descripcion, :tamano, :orden)
    ");
    
    foreach ($imagenes as $imagen) {
        $stmt->execute([
            ':tratamiento_id' => $imagen['tratamiento_id'],
            ':ruta_archivo' => $imagen['ruta_archivo'],
            ':nombre_archivo' => $imagen['nombre_archivo'],
            ':tipo_archivo' => $imagen['tipo_archivo'],
            ':descripcion' => $imagen['descripcion'],
            ':tamano' => $imagen['tamano'],
            ':orden' => $imagen['orden']
        ]);
    }
    
    echo "Insertadas " . count($imagenes) . " imágenes de tratamientos\n";
}

/**
 * Función auxiliar para obtener la extensión según el tipo MIME
 */
function obtenerExtension($tipoMime) {
    switch ($tipoMime) {
        case 'image/jpeg':
            return '.jpg';
        case 'image/png':
            return '.png';
        case 'application/pdf':
            return '.pdf';
        case 'image/dicom':
            return '.dcm';
        default:
            return '.dat';
    }
}