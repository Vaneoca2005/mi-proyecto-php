<?php
include("secure_area.php");
class EstudianteController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->model("Estudiante");
    }

    function index() {
        
        //consulta en data;
        $data['estudiantes'] = $this->Estudiante->get_info();
       $this->load->view("estudiante/index",$data);
    }

}
