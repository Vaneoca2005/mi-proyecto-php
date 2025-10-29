<?php
include("secure_area.php");
class AjusteController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->library("Session");
        $this->load->helper("url");
        $this->load->helper("auxiliar");
        $this->load->model("Ajuste");
    }
    function agregar_pdf() {

        $this->load->view("ajuste/Manual de Usuario.pdf");
    }

    function agregar() {

        $this->load->view("ajuste/agregar");
    }

    function index() {
        $data["all_cilindros"] = $this->Ajuste->get_all();
        // $this->load->view("ajuste/index", $data);

        $data["all_metodos"] = $this->Ajuste->get_all2();
        $this->load->view("ajuste/index", $data);
    }
    function agregar_metodo($id = NULL) {
    $data["id"] = $id;
    $data["metodo_pago"] = trim($_POST["metodo_pago"]); // quitar espacios al inicio/final

    $errors = [];

    // Validaciones Metodo de Pago
    if (empty($data["metodo_pago"])) {
        $errors[] = "El campo Método de Pago es obligatorio.";
    } elseif (strlen($data["metodo_pago"]) > 50) {
        $errors[] = "El campo Método de Pago no puede tener más de 50 caracteres.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s]+$/u", $data["metodo_pago"])) {
        $errors[] = "El campo Método de Pago solo puede contener letras, números y espacios.";
    }

    // Si hay errores, redirige al formulario agregar() con los datos
    if (count($errors) > 0) {
        $this->session->set_flashdata("error", implode("<br>", $errors));
        $this->session->set_flashdata("post_data", $data); // para rellenar formulario
        redirect("ajuste/agregar" . ($id ? "/".$id : ""));
        return;
    }
    // Guardar datos
    if ($this->Ajuste->agregar_metodo($data)) {
        $this->session->set_flashdata("success", "Datos registrados exitosamente!");
        redirect("ajuste/index");
    } else {
        $this->session->set_flashdata("error", "Los Datos no fueron registrados!");
        redirect("ajuste/agregar" . ($id ? "/".$id : ""));
    }
}


  function save($id = NULL) {
    $data["id"] = $id;
    $data["tamano"] = trim($_POST["tamano"]);
    $data["precio"] = trim($_POST["precio"]);

    $errors = [];

    // Validaciones Tamaño
    if (empty($data["tamano"])) {
        $errors[] = "El campo Tamaño es obligatorio.";
    } elseif (strlen($data["tamano"]) > 10) {
        $errors[] = "El campo Tamaño no puede tener más de 10 caracteres.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/u", $data["tamano"])) {
        $errors[] = "El campo Tamaño solo puede contener letras y un único espacio entre palabras.";
    }

    // Validaciones Precio
    if (empty($data["precio"])) {
        $errors[] = "El campo Precio es obligatorio.";
    } elseif (!is_numeric($data["precio"]) || $data["precio"] <= 0) {
        $errors[] = "El campo Precio debe ser un número mayor a 0.";
    } elseif (strlen((string)(int)$data["precio"]) > 6) {
        $errors[] = "El campo Precio no puede tener más de 6 dígitos.";
    }

    // Si hay errores, redirige al mismo edit() con los datos
    if (count($errors) > 0) {
        $this->session->set_flashdata("error", implode("<br>", $errors));
        $this->session->set_flashdata("post_data", $data); // Para rellenar el formulario
        redirect("ajuste/edit/".$id);
        return;
    }

    // Guardar datos sin importar si cambiaron o no
    if ($this->Ajuste->save_update($data)) {
        $this->session->set_flashdata("success", "Datos actualizados exitosamente!");
        redirect("ajuste/index");
    } else {
        $this->session->set_flashdata("error", "Los datos no fueron actualizados!");
        redirect("ajuste/index");
    }
}




    function edit($id = null) {
        $data["Ajuste"] = $this->Ajuste->get_info_by_id($id);
        $this->load->view("ajuste/add_update", $data);
    }

    function cerrar_sesion() {
        $this->session->sess_destroy();
        redirect("login");
    }
}