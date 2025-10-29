<?php
include("secure_area.php");
class LiderController extends secure_area
{
    function __construct()
    {
        parent::__construct();
        $this->load->library("Session");
        $this->load->helper("url");
        $this->load->helper("auxiliar");
        $this->load->model("Lider");
    }
    function index()
    {
        $data["all_lider"] = $this->Lider->get_all();
        $this->load->view("lider/lista", $data);
    }

    function create()
    {
        $this->load->view("lider/index");
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
    function save($id = null)
    {
        $errors = array();
        /*obtenemos lso datos del POST, recuerden que los datos delo POST vienen de los name del formulario */
        $data["cedula"] = $_POST["cedula"];
        $data["nombre"] = $_POST["nombre"];
        $data["apellido"] = $_POST["apellido"];
        $data["telefono"] = $_POST["telefono"];
        $data["correo"] = $_POST["correo"];
        $data["nombre_calle"] = $_POST["nombre_calle"];

        /**validamos los datos, vamos agregando "errores" */
        if (empty($data["cedula"])) {
            $errors[] = "No se aceptan campos en blanco en cedula";
        }
        if (!is_numeric($data["cedula"])) {
            $errors[] = "Solo se aceptan números en Cedula";
        }
        if (strlen($data["cedula"]) < 6) {
            $errors[] = "Mínimo 6 dígitos en Cédula";
        }
        if (strlen($data["cedula"]) > 8) {
            $errors[] = "Máximo 8 dígitos en Cédula";
        }
        /**Nombre */
        if (empty($data["nombre"])) {
            $errors[] = "No se aceptan campos en blanco en nombre";
        }
        if (is_numeric($data["nombre"])) {
            $errors[] = "No se aceptan números en Nombre";
        }
        if (strlen($data["nombre"]) < 3) {
            $errors[] = "Mínimo 3 carácteres en Nombre";
        }
        if (strlen($data["nombre"]) > 12) {
            $errors[] = "Máximo 12 caráteres en Nombre";
        }
        /**Apellido */
        if (empty($data["apellido"])) {
            $errors[] = "No se aceptan campos en blanco en apellido";
        }
        if (is_numeric($data["apellido"])) {
            $errors[] = "No se aceptan números en Apellido";
        }
        if (strlen($data["apellido"]) < 3) {
            $errors[] = "Mínimo 3 carácteres en Apellido";
        }
        if (strlen($data["apellido"]) > 12) {
            $errors[] = "Máximo 12 caráteres en Apellido";
        }
        /**Telefono*/
        if (empty($data["telefono"])) {
            $errors[] = "No se aceptan campos en blanco en Teléfono";
        }
        if (!is_numeric($data["telefono"])) {
            $errors[] = "Solo se aceptan números en Teléfono";
        }
        if (strlen($data["telefono"]) < 11) {
            $errors[] = "Mínimo 11 carácteres en Telefono";
        }
        if (empty($data["correo"])) {
            $errors[] = "No se aceptan campos en blanco en correo";
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
                redirect("lider/index");
            }
            redirect("lider/create");
        }
        if ($this->Lider->save($data)) {
            $this->session->set_flashdata("success", "El Jefe fue creado exitosamente!");
        } else {
            $this->session->set_flashdata("danger", "Registro Fallido!");
        }


        redirect("lider");
    }

    function view()
    {
    }

    function cerrar_sesion()
    {
        $this->session->sess_destroy();
        redirect("login");
    }
}
