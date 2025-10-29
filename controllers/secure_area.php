<?php
class secure_area extends Controller {
    function __construct() {
        parent::__construct();
        $this->load->library("Session");
        $this->load->helper("url");
        $this->load->helper("auxiliar");
        $this->load->model("User");

        if (!$this->User->is_logged_in()) {
            redirect('login');
        }

        /**cargar la data de POST para cuando se redirecciona */
        if ($this->session->userdata("POST_DATA")) {
            $this->load->vars($this->session->userdata("POST_DATA"));
            $this->session->unset_userdata("POST_DATA");
        }

        $data["user_info"] = $this->User->get_info();

        /**validación para que el usuario sepa que debe cambiar su contraseña */
        if ($data["user_info"]->force_password_change) {
            // if ($this->uri->rsegment(2) !== "perfil") {
            //     redirect('user/perfil');
            // }
            $this->session->set_flashdata("warning", "Por favor ingresa a tu <a href=" . site_url("user/perfil") . ">perfil<a/> y actualiza tus datos!");
        }

        $this->load->vars($data);
    }
}
