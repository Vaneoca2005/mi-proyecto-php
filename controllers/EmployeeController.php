<?php
include("secure_area.php");
class EmployeeController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->model('Employee');
    }

    function index() {
        /**esto nos permite establecer los parámetros de búsqueda la primera vez */
        if (!$this->session->userdata('search_data_jefes')) {
            $params = [
                "calle" => "",
                "cedula" => ""


            ];
            $this->session->set_userdata('search_data_jefes', $params);
        }

        /**asi cargamos los parámetros de búsqueda, si se modifican en la función search entonces tomamos los valores */
        $params = $this->session->userdata('search_data_jefes');

        /**copiamos los datos de los parameters a data, para pasarlos a la vista también */
        $data = $params;

        /**agregamos un nuevo indice a data con la información de los empleados */
        $data["jefes"] = $this->Employee->get_all($params);

        /**cargamos la vista */
        $this->load->view("empleado/lista_jefes", $data);
    }

    function search() {
        /**recuperamos los parámetros */
        $params = $this->session->userdata('search_data_jefes');
        /**establecemos los nuevos parámetros */
        $params["cedula"] = $_POST["cedula"];
        $params["calle"] = $_POST["calle"];

        /**set params to session var for index */
        $this->session->set_userdata('search_data_jefes', $params);
        /**redireccionamos */

        redirect('employee');
    }

    function limpiar_search() {
        /**eliminamos los parámetros de la sesión lo cual reinicia la búsqueda */
        $this->session->unset_userdata('search_data_jefes');
        redirect('employee');
    }
}