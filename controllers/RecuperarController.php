<?php
class RecuperarController extends Controller {
    function __construct() {
        parent::__construct();
        $this->load->library("Session");
        $this->load->helper("url");
        $this->load->helper("auxiliar");
        $this->load->Model("Recuperar");
        $this->load->Model("User");
        $this->load->Model("Respuesta");
    }

    function index() {
        $this->load->view("recuperar/index");
    }

    function validar_usuario() {
        $identidad = $_POST["username"];

        if (empty($identidad)) {
            $this->session->set_flashdata("error", "El usuario es requerido");
            redirect("recuperar/index");
            exit;
        }

        $user_info = $this->User->get_info_by_username(["username" => $identidad]);
        if (!$user_info) {
            $this->session->set_flashdata("error", "El usuario no existe en el sistema");
            redirect("recuperar/index");
            exit;
        }

        $id_user = $user_info->id;
        // Obtén la información de las respuestas de seguridad del usuario
        $data["user_info"] = $user_info;
        $data["preguntas"] = $this->Respuesta->get_info_by_user_id(["usuario_id" => $id_user]);

        if (count($data['preguntas']) == 0) {
            $this->session->set_flashdata("error", "Este usuario no ha actualizado su perfil");
            redirect("recuperar/index");
        }

        // Carga la vista con las preguntas de seguridad
        $this->load->view("recuperar/pregunta", $data);
    }
   


    function password() {
        $id_user = $_POST['username'];
        $respuesta_field = $this->Respuesta->get_info_by_user_id(["usuario_id" => $id_user]);
        $respuesta_post = $_POST['respuesta'];

        foreach ($respuesta_field as $value) {
            if (!password_verify($respuesta_post[$value->pregunta_id], $value->respuesta)) {
                $this->session->set_flashdata("error", "Las respuestas son incorrectas");
              
                redirect("recuperar");
            }
        }

        $this->load->view("recuperar/password", ['user_id' => $id_user]);
    }

     function update_password() {
        /**validar todo de la contraseña */

        $pass  = $_POST['password'];
        $user_id = $_POST['user_id'];
        if ($this->User->actualizar_password(['password' => password_hash($pass, PASSWORD_BCRYPT), 'id' => $user_id])) {
            $this->session->set_flashdata("success", "La contraseña fue actualizada!");
            redirect("login/index");
        }
    }

    
   
}