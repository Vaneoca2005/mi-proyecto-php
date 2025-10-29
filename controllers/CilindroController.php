<?php
include("secure_area.php");
class CilindroController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->library("Session");
        $this->load->helper("url");
        $this->load->helper("auxiliar");
        $this->load->model("Cilindro");
    }
    function index() {
        $data["all_users"] = $this->Cilindro->get_all();
        $this->load->view("cilindro/index", $data);
    }

    function create() {
        $this->load->view("cilindro/index");
    }

    /*@NOTA este seria el método editar si se permitiera editar a los usuarios, este método es un ejemplo de como hacerlo, pueden usarlos para los demás módulos donde si se permite la edición */
    // function edit($id = null) {
    //     /**con el id busca el usuario y llena el data*/
    //     $data = $this->User->get_info($id);
    //     $this->load->view("usuario/add_update", $data);
    // }


    /**
     * este metodo sirve para crear un usuario nuevo y para actualizarlo, cuando entramos por "create" no pasamos $id por entre se entiende
     * que se trata de un nuevo usuario
     * en el metodo "perfil" si tomamos el $id del usuario en sesión, y al existir este el método save "toma una serie de decisiones if" que
     * modifican su comportamiento, o sea, si hay ID va a actualizar los datos.
     * 
     */
    function save($id = null) {
        $errors = array();
        /*obtenemos lso datos del POST, recuerden que los datos delo POST vienen de los name del formulario */
        $data["empresa"] = $_POST["empresa"];
        $data["tipo_de_boca"] = $_POST["tipo_de_boca"];
        $data["tamano"] = $_POST["tamano"];
        $data["cantidad"] = $_POST["cantidad"];
        $data["jefe_de_familia_id"] = $_POST["jefe_de_familia"];

        /**validamos los datos, vamos agregando "errores" */
        if (empty($data["empresa"])) {
            $errors[] = "No se aceptan campos en blanco en empresa";
        }
        if (empty($data["tipo_de_boca"])) {
            $errors[] = "No se aceptan campos en blanco tipo de boca";
        }
        if (empty($data["tamano"])) {
            $errors[] = "No se aceptan campos en blanco en nombre";
        }


        //dd($errors);

        /**si hay errores entonces redirigimos sin guardar */
        if (count($errors) > 0) {
            /**este método auxiliar sirve para cargar los datos en el load->vars y asi al redireccionar a los métodos que usan la vista "add_update" se pueda cargar los datos en los campos, si no se usa al redireccionar notaras que los campos se vacían :/
             */
            set_post_data($data);
            $this->session->set_flashdata("error", $errors);
            /**si hay ID nos vamos a perfil sino es xq estaba en create */
            if ($id != null) {
                redirect("cilindro/index");
            }
            redirect("cilindro/create");
        }
        if ($this->Cilindro->save($data)) {
            $this->session->set_flashdata("success", "El Cilindro fue registrado exitosamente!");
        } else {
            $this->session->set_flashdata("danger", "Registro Fallido!");
        }


        redirect("cilindro");
    }

    function view() {
    }

    function cerrar_sesion() {
        $this->session->sess_destroy();
        redirect("login");
    }
}