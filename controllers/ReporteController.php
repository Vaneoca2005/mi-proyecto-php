<?php
include("secure_area.php");


class ReporteController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->library("Reporte");
        $this->load->model("Estudiante");
        $this->load->model('Employee');
        $this->load->model('Rjornada');
    }


    function index($id = null) {
        $data['estudiantes'] = $this->Estudiante->get_All();
        $this->reporte->view("reports/users_report", $data);

        $this->reporte->set_parameters("P", "A4");
        $this->reporte->generatePDF("reporte de ventas.pdf");
    }

    function consulta() {
        $data['estudiantes'] = $this->Estudiante->get_All();
        $this->reporte->view("reports/report_sale", $data);

        $this->reporte->set_parameters("P", "A4");
        $this->reporte->generatePDF("ejemplo 7.pdf");
    }
    function listado_general_usuarios() {
        $consulta['id'] = $_POST['nombre'];


        $data['estudiantes'] = $this->Estudiante->get_All($consulta);

        $this->reporte->view("reports/listado_general_usuarios", $data);

        $this->reporte->set_parameters("P", "A4");
        $this->reporte->generatePDF("Listado general de Usuarios.pdf", "D");
    }


    function listado_jefes() {
        $params = $this->session->userdata('search_data_jefes');
        /**copiamos los datos de los parameters a data, para pasarlos al reporte, por si quieren mostrar
         * que es lo que están filtrando
         */
        $data = $params;
        /**agregamos un nuevo indice a data con la información de los empleados */
        $data["lista_jefe"] = $this->Employee->get_all($params);

        /**cargamos la vista del reporte */
        $this->reporte->view("reports/listado_jefes", $data);

        $this->reporte->set_parameters("P", "A4");
        $this->reporte->generatePDF("Listado de Jefes de Familia.pdf", "D");
    }
    function listado_jornada() {
        $params = $this->session->userdata('search_data_jornada');
        /**copiamos los datos de los parameters a data, para pasarlos al reporte, por si quieren mostrar
         * que es lo que están filtrando
         */
        $data = $params;
        /**agregamos un nuevo indice a data con la información de los empleados */
        $data["jornada"] = $this->Rjornada->get_all($params);

        /**cargamos la vista del reporte */
        $this->reporte->view("reports/listado_jornada", $data);

        $this->reporte->set_parameters("L", "A4");
        $this->reporte->generatePDF("Listado de Jornada.pdf", "D");
    }
}