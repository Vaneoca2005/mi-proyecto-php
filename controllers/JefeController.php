<?php
include("secure_area.php");
class JefeController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->library("Session");
        $this->load->helper("url");
        $this->load->helper("auxiliar");
        $this->load->model("Jefe");
    }
    function index() {
    $step = isset($_GET['step']) ? (int)$_GET['step'] : null;

    if ($step) {
        // Si viene un step, cargar la vista del formulario y pasarle $step
        $data["currentStep"] = $step;
        $data["calles"] = $this->Jefe->get_calles();
        $this->load->view("jefe/index", $data);
    } else {
        // Si no hay step, mostrar lista
        $data["all_users"] = $this->Jefe->get_all();
        $this->load->view("jefe/lista", $data);
    }
}


   function create() {
    $data["calles"] = $this->Jefe->get_calles();
    $this->load->view("jefe/index", $data);
    
}

function edit($id = null) {
    $data["Jefe"] = $this->Jefe->get_info_by_id($id);
    $data["calles"] = $this->Jefe->get_all_calles();
    $data["cargas"] = $this->Jefe->get_cargas_by_jefe($id);
    $data["currentStep"] = isset($_GET['step']) ? $_GET['step'] : 1;
    $this->load->view("jefe/index", $data);
}

public function eliminar_carga($id = null) { 
    $data["id"] = $id; 

    if ($this->Jefe->delete_carga($data)) { 
        $this->session->set_flashdata("success", "Registro eliminado correctamente"); 
    } else { 
        $this->session->set_flashdata("danger", "No se pudo eliminar el registro"); 
    } 

    // Tomamos el paso y jefe_id directamente desde $_GET
    $step = isset($_GET['step']) ? $_GET['step'] : 1;
    $jefe_id = isset($_GET['jefe_id']) ? $_GET['jefe_id'] : 0;

    
    redirect("jefe/edit/$jefe_id?step=$step");
}
  
   
    function save() {
        $errors = array();
        // --------------------- CAPTURAR ID SI VIENE ---------------------
    $id = $_POST['id'] ?? null;

    // --------------------- CAPTURAR DATOS DEL FORMULARIO ---------------------
    $data = [
        "cedula"             => htmlspecialchars(trim($_POST["cedula"])),
        "nombre"             => htmlspecialchars(trim($_POST["nombre"])),
        "apellido"           => htmlspecialchars(trim($_POST["apellido"])),
        "fecha_de_nacimiento"=> htmlspecialchars(trim($_POST["fecha_de_nacimiento"])),
        "edad"               => htmlspecialchars(trim($_POST["edad"])),
        "genero"             => htmlspecialchars(trim($_POST["genero"])),
        "telefono"           => htmlspecialchars(trim($_POST["telefono"])),
        "correo"             => htmlspecialchars(trim($_POST["correo"])),
        "calle_id"           => htmlspecialchars(trim($_POST["calle_id"])),
        "numero_de_casa"     => htmlspecialchars(trim($_POST["numero_de_casa"])),
        "estado"             => htmlspecialchars(trim($_POST["estado"])),
        "municipio"          => htmlspecialchars(trim($_POST["municipio"])),
        "parroquia"          => htmlspecialchars(trim($_POST["parroquia"])),
        "lider_de_calle"     => htmlspecialchars(trim($_POST["lider_de_calle"])),
        "discapacidad"       => htmlspecialchars(trim($_POST["discapacidad"])),
        "tipo_discapacidad"  => htmlspecialchars(trim($_POST["tipo_discapacidad"])),
        "nivel_de_estudio"   => htmlspecialchars(trim($_POST["nivel_de_estudio"])),
        "sabe_leer"          => htmlspecialchars(trim($_POST["sabe_leer"])),
        "sabe_escribir"      => htmlspecialchars(trim($_POST["sabe_escribir"]))
    ];


    // ---------------- VALIDACIONES ----------------
    if (empty($data["cedula"]) || !is_numeric($data["cedula"]) || strlen($data["cedula"]) < 6 || strlen($data["cedula"]) > 8) {
        $errors[] = "La cédula debe tener entre 6 y 8 dígitos y solo números";
    }

    if (empty($data["nombre"]) || is_numeric($data["nombre"]) || strlen($data["nombre"]) < 3 || strlen($data["nombre"]) > 25) {
        $errors[] = "El nombre debe tener entre 3 y 25 caracteres y no contener números";
    }

    if (empty($data["apellido"]) || is_numeric($data["apellido"]) || strlen($data["apellido"]) < 3 || strlen($data["apellido"]) > 25) {
        $errors[] = "El apellido debe tener entre 3 y 25 caracteres y no contener números";
    }

    if (empty($data["telefono"]) || !is_numeric($data["telefono"]) || strlen($data["telefono"]) != 11) {
        $errors[] = "El teléfono debe tener exactamente 11 dígitos y solo números";
    }

    if (empty($data["correo"])) {
        $errors[] = "El correo no puede estar vacío";
    }

    if (empty($data["fecha_de_nacimiento"])) {
        $errors[] = "Debe ingresar la fecha de nacimiento";
    }

    if (!in_array($data["genero"], ["Masculino", "Femenino"])) {
        $errors[] = "Seleccione un género válido";
    }

    // Dirección
    if ($data["calle_id"] == 0) {
        $errors[] = "Seleccione una calle válida";
    }
    if (empty($data["numero_de_casa"])) {
        $errors[] = "Ingrese el número de casa";
    }
    if (empty($data["estado"])) {
        $errors[] = "Ingrese el estado";
    }
    if (empty($data["municipio"])) {
        $errors[] = "Ingrese el municipio";
    }
    if (empty($data["parroquia"])) {
        $errors[] = "Ingrese la parroquia";
    }
    if (!in_array($data["lider_de_calle"], ["Sí", "No"])) {
        $errors[] = "Seleccione si es líder de calle";
    }

    // Antecedentes médicos
    if (!in_array($data["discapacidad"], ["Sí", "No"])) {
        $errors[] = "Seleccione si tiene discapacidad";
    }
    if ($data["discapacidad"] == "Sí" && empty($data["tipo_discapacidad"])) {
        $errors[] = "Debe indicar el tipo de discapacidad";
    }

    // Datos académicos
    if (!in_array($data["sabe_leer"], ["Sí", "No"])) {
       $errors[] = "Seleccione si sabe leer";
    }
    if (!in_array($data["sabe_escribir"], ["Sí", "No"])) {
        $errors[] = "Seleccione si sabe escribir";
    }
    if (empty($data["nivel_de_estudio"])) {
        $errors[] = "Seleccione el nivel de estudio";
    }

   
    // ---------------- VALIDACIÓN CARGA FAMILIAR ----------------
$cf_nombre      = $_POST['cf_nombre'] ?? [];
$cf_apellido    = $_POST['cf_apellido'] ?? [];
$cf_cedula      = $_POST['cf_cedula'] ?? [];
$cf_edad        = $_POST['cf_edad'] ?? [];
$cf_parentesco  = $_POST['cf_parentesco'] ?? [];
$cf_id          = $_POST['cf_id'] ?? []; // Hidden, contiene el id si existe

for ($i = 0; $i < count($cf_nombre); $i++) {
    // Validar nombre
    if (empty($cf_nombre[$i]) || is_numeric($cf_nombre[$i]) || strlen($cf_nombre[$i]) < 3 || strlen($cf_nombre[$i]) > 25) {
        $errors[] = "Nombre inválido en la carga familiar fila ".($i+1);
    }

    // Validar apellido
    if (empty($cf_apellido[$i]) || is_numeric($cf_apellido[$i]) || strlen($cf_apellido[$i]) < 3 || strlen($cf_apellido[$i]) > 25) {
        $errors[] = "Apellido inválido en la carga familiar fila ".($i+1);
    }

    // Validar cédula
    if (empty($cf_cedula[$i]) || !is_numeric($cf_cedula[$i]) || strlen($cf_cedula[$i]) < 6 || strlen($cf_cedula[$i]) > 8) {
        $errors[] = "Cédula inválida en la carga familiar fila ".($i+1);
    }

    // Validar edad
    if (empty($cf_edad[$i]) || !is_numeric($cf_edad[$i]) || $cf_edad[$i] < 0 || $cf_edad[$i] > 120) {
        $errors[] = "Edad inválida en la carga familiar fila ".($i+1);
    }

    // Validar parentesco
    if (empty($cf_parentesco[$i])) {
        $errors[] = "Parentesco obligatorio en la carga familiar fila ".($i+1);
    }
}

// Si hay errores, redirigir con mensajes
if (count($errors) > 0) {
    // Guardar los datos del formulario para rellenarlos luego
    $this->session->set_flashdata("post_data", $data);

    // Guardar datos de la carga familiar
    $cf_data = [];
    for ($i = 0; $i < count($cf_nombre); $i++) {
        $cf_data[] = [
            "id"         => $cf_id[$i] ?? null,
            "nombre"     => $cf_nombre[$i] ?? '',
            "apellido"   => $cf_apellido[$i] ?? '',
            "cedula"     => $cf_cedula[$i] ?? '',
            "edad"       => $cf_edad[$i] ?? '',
            "parentesco" => $cf_parentesco[$i] ?? '',
        ];
    }
    $this->session->set_flashdata("cf_data", $cf_data);

    // Guardar los errores
    $this->session->set_flashdata("error", $errors);

    // Redirigir al formulario de edición o creación según corresponda
    redirect($id ? "jefe/edit/$id" : "jefe/create");
}



   // --------------------- GUARDAR O ACTUALIZAR JEFE ---------------------
if ($id == null) {
    $jefe_id = $this->Jefe->save($data);
} else {
    $data['id'] = $id;
    $this->Jefe->update_jefe($data);
    $jefe_id = $id;
}

// --------------------- GUARDAR/ACTUALIZAR/ELIMINAR CARGA FAMILIAR ---------------------
$cargas_enviadas = []; // IDs de las cargas que vienen desde el formulario

for ($i = 0; $i < count($cf_nombre); $i++) {
    $carga_id = !empty($cf_id[$i]) ? $cf_id[$i] : null;

    $carga_data = [
        "jefe_id"    => $jefe_id,
        "cedula"     => htmlspecialchars(trim($cf_cedula[$i])),
        "nombre"     => htmlspecialchars(trim($cf_nombre[$i])),
        "apellido"   => htmlspecialchars(trim($cf_apellido[$i])),
        "edad"       => htmlspecialchars(trim($cf_edad[$i])),
        "parentesco" => htmlspecialchars(trim($cf_parentesco[$i])),
    ];

    if ($carga_id) {
        // Actualizar carga existente
        $carga_data["id"] = $carga_id;
        $this->Jefe->update_carga($carga_data);
        $cargas_enviadas[] = $carga_id;
    } else {
        // Insertar nueva carga
        $new_id = $this->Jefe->save_carga_familiar($carga_data);
        $cargas_enviadas[] = $new_id;
    }
}

// --------------------- MENSAJE Y REDIRECCIÓN ---------------------
$this->session->set_flashdata(
    "success",
    $id ? "El Jefe fue actualizado exitosamente!" : "El Jefe fue creado exitosamente!"
);
redirect("jefe");
}

   
    function eliminar($id = null) {

        $data["id"] = $id;

        

        // Intentar eliminar el Jefe
        if ($this->Jefe->delete_status($data)) {
            $this->session->set_flashdata("success", "El Jefe fue eliminado exitosamente!");
        } else {
            // Si no se pudo eliminar, establecer un mensaje de error
            $this->session->set_flashdata('danger', 'No se pudo borrar el Jefe. Inténtalo de nuevo.');
        }

        // Redirigir a la página de índice de usuarios
        redirect("jefe");
    }


    function view() {
    }

    function cerrar_sesion() {
        $this->session->sess_destroy();
        redirect("login");
    }
}