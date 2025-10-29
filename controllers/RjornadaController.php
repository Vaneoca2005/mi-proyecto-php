<?php
include("secure_area.php");
class RjornadaController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->model('Rjornada');
    }


    function index() {
        /**esto nos permite establecer los parámetros de búsqueda la primera vez */
        if (!$this->session->userdata('search_data_jornada')) {
            $params = [
                "fecha_in" => "",
                "fecha_out" => "",
                "nombre" => "",
                "cedula" => ""


            ];
            $this->session->set_userdata('search_data_jornada', $params);

        }

        /**asi cargamos los parámetros de búsqueda, si se modifican en la función search entonces tomamos los valores */
        $params = $this->session->userdata('search_data_jornada');

        /**copiamos los datos de los parameters a data, para pasarlos a la vista también */
        $data = $params;

        /**agregamos un nuevo indice a data con la información de los empleados */
        

        $data["lista"] = $this->Rjornada->get_all($params);

        /**cargamos la vista */
        $this->load->view("jornada/lista", $data);
    }

    function search() {
        /**recuperamos los parámetros */

        $params = $this->session->userdata('search_data_jornada');

        
        /**establecemos los nuevos parámetros */
        $params["fecha_in"] = $_POST["fecha_in"];
        $params["fecha_out"] = $_POST["fecha_out"];
        $params["nombre"] = $_POST["nombre"];
        $params["cedula"] = $_POST["cedula"];

        /**set params to session var for index */
        $this->session->set_userdata('search_data_jornada', $params);
        /**redireccionamos */

        redirect('rjornada');
    }

    function limpiar_search() {
        /**eliminamos los parámetros de la sesión lo cual reinicia la búsqueda */
        $this->session->unset_userdata('search_data_jornada');
        redirect('rjornada');
    }
}