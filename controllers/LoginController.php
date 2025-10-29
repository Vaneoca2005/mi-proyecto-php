<?php
class LoginController extends Controller {
    function __construct() {
        parent::__construct();
        $this->load->library("Session");
        $this->load->helper("url");
        $this->load->helper("auxiliar");
        $this->load->Model("User");
        $this->load->model("Respuesta");

        if ($this->User->is_logged_in()) {
            redirect('home');
        }
    }

    function index() {
        $this->load->view("login/index");
    }
    
   
         
    function iniciar_sesion() {

        $username = $_POST["username"];
        $pass = $_POST["password"];

        $user_info = $this->User->get_info_by_username(["username" => $username]);

        // dd($user_info);

        if ($user_info) {
            if (!$user_info->status) {
                $this->session->set_flashdata("error", "Este usuario esta inactivo!");
                redirect("login");
                exit;
            }

            if (!password_verify($pass, $user_info->password)) {
                $this->session->set_flashdata("error", "Contraseña incorrecta por favor verifique!");
                redirect("login");
                exit;
            }
            $this->session->set_userdata('user_id', $user_info->id);
            redirect("home");
            exit;
        }

        $this->session->set_flashdata("error", "El usuario no existe en este sistema!");
        redirect("login");
        exit;
    }
}