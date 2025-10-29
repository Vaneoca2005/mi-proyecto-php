<?php
include("secure_area.php");
class MantenimientoController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->model("Mantenimiento");
    }

    function index() {
        $data["all_backup"] = $this->Mantenimiento->get_all();

        $this->load->view("mantenimiento/respaldo_list", $data);
    }
       

    function generar_respaldo() {
        //Datos de la base de datos
        $database = $this->config->item("database");
        $user = $this->config->item("user");
        $password = $this->config->item("password");
        $host = $this->config->item("host");
        // $nombre_archivo = $this->_generar_nombre_aleatorio();
        $fecha = date("Y-m-d-H_i_s");
        $nombre_archivo = "database_backup_" . $fecha;
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $path = 'backup/' . $nombre_archivo . '.sql';
        // Backup con mysqldump
        $command = "C:\\xampp\\mysql\\bin\\mysqldump --opt -h" . $host . " -u" . $user . " " . $password . " " . $database . "  > " . $path;

        $output = array();
        exec($command, $output, $worked);

        $data['usuario_id'] = $this->session->userdata('user_id');
        $data['navegador'] = $user_agent;
        $data['url'] = $path;
        $data['fecha_registro'] = $fecha;

    
        switch ($worked) {
            case 0:
                $this->session->set_flashdata("success", "El respaldo se ha realizado con éxito");
                $this->Mantenimiento->save_backup_data($data);

                redirect("mantenimiento");
                break;
            case 1:
                echo 'Se ha producido un error al exportar <b>' . $database . '</b> a ' . getcwd() . '/' . $path . '</b>';
                break;
            case 2:
                echo 'Se ha producido un error de exportación, compruebe la siguiente información: <br/><br/><table><tr><td>Nombre de la base de datos:</td><td><b>' . $database . '</b></td></tr><tr><td>Nombre de usuario MySQL:</td><td><b>' . $user . '</b></td></tr><tr><td>Contraseña MySQL:</td><td><b>NOTSHOWN</b></td></tr><tr><td>Nombre de host MySQL:</td><td><b>' . $host . '</b></td></tr></table>';
                break;
        }
    }

    function _generar_nombre_aleatorio($longitud = 12) {
        $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($caracteres_permitidos), 0, $longitud);
    }
}
