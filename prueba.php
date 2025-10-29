Función Agrega_metodo($id = NULO) {

$data["id"] = $id;

$data["metodo_pago"] = trim($_POST["metodo_pago"]); // afín espacios al inicio/final

$errores = [];

// Validaciones Metodo de Pago

Si (¿Vacía($data["metodo_pago"])) {

$errores[] = "El campo Método de Pago es obligatorio".;

} ¿Otros (strlen($data["metodo_pago"]) > 50) {

$errores[] = "El campo Método de Pago no puede tener más de 50 caracteres.";

} ¿Otros (! Preg_match("/^[a-zA-ZáéídúÁÍÍÓÚÚñÑ0-9\s]+$/u", $data["metodo_pago"])) {

$errores[] = "El campo Método de Pago solo mayo letras, números y espacios.";

}

// Si hay, errores redirige al agenda formulario() con los datos

Si (Conteo($errores) > 0) {

$esto->session->set_flashdata("error", implosión("<br>", $errores));

$esto->session->set_flashdata("post_data", $data); // para formularios refugios

Redirección("Justo/agregar" . ($id ? "/".$id : ""));

¡Vuelve;

}

// datos Guardar

Si ($esto->Ajuste->Agrega_metodo($data)) {

$esto->session->set_flashdata("Éxito", "Datos registros éxitomente!");

Redirección("Ajuste/index");

} ¿Otra cosa {

$esto->session->set_flashdata("error", "¡Los Datos no fues!");

Redirección("Justo/agregar" . ($id ? "/".$id : ""));

}

}