<?php
include("secure_area.php");
class JornadaController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->library("Session");
        $this->load->helper("url");
        $this->load->helper("auxiliar");
        $this->load->model("Jornada");
        $this->load->model("Jefe");
    }


    function index() {
        $data["all_jornada"] = array();
        $data["bombona_data"] = array();

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $params["cedula"] = $_POST["cedula"];
            $data["cedula"] = $this->Jefe->get_by_cedula($params)->id;
            $data["all_jornada"] = $this->Jornada->get_persona_jornada($params);
            $data["bombona_data"] = $this->Jornada->get_bombona_data($params);
        }

        $this->load->view("jornada/index", $data);
    }

    function save() {
        $errors = array();
        $bombonas = !empty($_POST['bombona_cantidad']) ? $_POST['bombona_cantidad'] : null;

        $data['jefe_id'] = $_POST['jefe_id'];
        $data['costo_total'] = $_POST['costo_total'];
        $data['metodo_pago_id'] = $_POST['metodo_de_pago'];
        $data['referencia'] = $_POST['referencia'];

        if ($bombonas == null) {
            $errors[] = "No se selecciono ninguna Bombona";
        }


        if (count($errors) > 0) {
            /**este método auxiliar sirve para cargar los datos en el load->vars y asi al redireccionar a los métodos que usan la vista "add_update" se pueda cargar los datos en los campos, si no se usa al redireccionar notaras que los campos se vacían :/
             */
            set_post_data($data);
            $this->session->set_flashdata("error", $errors);
            /**si hay ID nos vamos a perfil sino es xq estaba en create */
            if ($id != null) {
                redirect("jornada");
            }
            redirect("jornada");
        }

        //mensjae y redirrecion
        if ($data["jornada_id"] = $this->Jornada->save_jornada($data)) {
            $this->session->set_flashdata("success", "La Jornada fue Registrada Exitosamente!");
        } else {
            $this->session->set_flashdata("error", "La Jornada no pudo ser Registrada!");
        }

        foreach ($bombonas as $key => $value) {
            $precio = $this->Jornada->get_bombona_precio(["id" => $key])->precio;
            $data_detalle['tamano_id'] = $key;
            $data_detalle['cantidad_compra'] = $value;
            $data_detalle['precio'] = $precio;
            $data_detalle['jornada_id'] = $data['jornada_id'];
            $this->Jornada->save_jornada_detalle($data_detalle);
        }

        redirect("jornada/index");
    }
}