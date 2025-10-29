<?php
include("secure_area.php");
class UserController extends secure_area {
    function __construct() {
        parent::__construct();
        $this->load->model("Respuesta");
    }

    function index() {
        $data["all_users"] = $this->User->get_all();
        $this->load->view("usuario/manage", $data);
    }
    function edit($id = null) {
    $data["Usuario"] = $this->User->get_info($id);
    $this->load->view("usuario/add_update", $data);
}


    function create() {
        $this->load->view("usuario/add_update");
    }

    /*@NOTA este seria el método editar si se permitiera editar a los usuarios, este método es un ejemplo de como hacerlo, pueden usarlos para los demás módulos donde si se permite la edición */
    // function edit($id = null) {
    //     /**con el id busca el usuario y llena el data*/
    //     $data = $this->User->get_info($id);
    //     $this->load->view("usuario/add_update", $data);
    // }

    function perfil()
{
    // Datos del usuario actual
    $data = $this->User->get_info();

    // ID del usuario logueado
    $usuario_id = $this->session->userdata('user_id');

    // Todas las preguntas
    $data->preguntas = $this->User->get_preguntas();

    // Respuestas actuales del usuario
    $data->respuestas = $this->User->get_respuestas_usuario($usuario_id);

    // Cargar vista
    $this->load->view("usuario/add_update", $data);
}

    public function update_foto($id) {
    if (!empty($_FILES['foto']['name'])) {
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name']     = 'perfil_'.$id.'_'.time();

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('foto')) {
            $fileData = $this->upload->data();
            $foto = $fileData['file_name'];

            // Guardar en base de datos
            $this->db->where('id', $id);
            $this->db->update('usuarios', ['foto' => $foto]);

            $this->session->set_flashdata('success', 'Foto actualizada correctamente');
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        }
    }
    redirect('user/perfil/'.$id);
}



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
        $data["username"] = $_POST["username"];
        $data["cedula"] = $_POST["cedula"];
        $data["nombre"] = $_POST["nombre"];
        $data["apellido"] = $_POST["apellido"];
        $data["correo"] = $_POST["correo"];
        $data["rol"] = $_POST["rol"];
        $data["password"] = $_POST["password"];
        $data["re_password"] = $_POST["re_password"];

        $data["pregunta"] = $_POST["pregunta"];
        $data["respuesta"] = $_POST["respuesta"];



        /**validamos los datos, vamos agregando "errores" */
        if (empty($data["cedula"])) {
            $errors[] = "No se aceptan campos en blanco en Cédula";
        }
        if (!is_numeric($data["cedula"])) {
            $errors[] = "No se aceptan carácteres en Cédula";
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
        /**Usuario*/
        if (empty($data["username"])) {
            $errors[] = "No se aceptan campos en blanco en usuario";
        }

        if (strlen($data["username"]) < 3) {
            $errors[] = "Mínimo 3 carácteres en Usuario";
        }
        /**Rol */
        if (($data["rol"] == 0)) {
            $errors[] = "Seleccione un tipo de usuario";
        }
        /**Password */
        if (empty($data["password"])) {
            $errors[] = "No se aceptan campos en blanco en contraseña";
        }
        if (strlen($data["password"]) < 8) {
            $errors[] = "Mínimo 8 carácteres en Contraseña";
        }
        if (strlen($data["password"]) > 16) {
            $errors[] = "Máximo 16 caráteres en Contraseña";
        }
        if (!preg_match('/[A-Z]/', $data["password"])) {
            $errors[] = "La contraseña debe contener al menos una letra mayúscula";
        }
        if (!preg_match('/[a-z]/', $data["password"])) {
            $errors[] = "La contraseña debe contener al menos una letra minúscula";
        }
        if (!preg_match('/[0-9]/', $data["password"])) {
            $errors[] = "La contraseña debe contener al menos un número";
        }
        if (!preg_match('/[\W]/', $data["password"])) {
            $errors[] = "La contraseña debe contener al menos un carácter especial";
        }

        /**hay ID validación cuando es update */
        if ($id != null) {
            /**cuando se hace update validamos las preguntas de seguridad que en el formulario de registro no esta, puedes revisar
             * el archivo add_update y busca la parte del metodo rsegment con esa lógica es que habilitamos las pregunta de seguridad
             * en el "perfil" y no en al momento de crear el usuario
             */

            foreach ($data['pregunta'] as $value) {
                if ($value == 0) {
                    $errors[] = "Falta una pregunta por seleccionar";
                    break;
                }
            }
            /**recuerda las validaciones especiales, como numero de caracteres máximo, mínimo o correo electrónicos etc, en este caso
             * se valida que las contraseñas coincidan
             */
            if ($data['password'] !== $data['re_password']) {
                $errors[] = "las contraseñas no coinciden";
            }
        }

        /**si hay errores entonces redirigimos sin guardar */
        if (count($errors) > 0) {
            /**este método auxiliar sirve para cargar los datos en el load->vars y asi al redireccionar a los métodos que usan la vista "add_update" se pueda cargar los datos en los campos, si no se usa al redireccionar notaras que los campos se vacían :/
             */
            set_post_data($data);
            $this->session->set_flashdata("error", $errors);
            /**si hay ID nos vamos a perfil sino es xq estaba en create */
            if ($id != null) {
                redirect("user/perfil");
            }
            redirect("user/create");
        }


        /**pasamos la validación , si no hay ID era create sino, es create */
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        if ($id == null) {
            /**registro de nuevo usuario */
            if ($this->User->save($data)) {
                $this->session->set_flashdata("success", "El usuario fue creado exitosamente!");
            }
        } else {
            $data['id'] = $id;

            if ($this->User->save_update($data)) {
                /**actualizo las preguntas */

                $respuesta = $this->Respuesta->get_info_by_user_id(["usuario_id" => $data['id']]);

                if (!empty($respuesta)) {
                    $this->Respuesta->eliminar(['usuario_id' => $data['id']]);
                }

                foreach ($data["pregunta"] as $key => $pregunta_id) {
                    $res_data["pregunta_id"] = $pregunta_id;
                    $res_data['respuesta'] = password_hash($data['respuesta'][$key], PASSWORD_BCRYPT);
                    $res_data['usuario_id'] = $data['id_usuario'] = $data['id'];
                    $this->Respuesta->save($res_data);
                }
                $this->session->set_flashdata("success", "Datos del usuario actualizados exitosamente!");
            }
        }
        redirect("user");
    }

    function view() {
    }

    function cerrar_sesion() {
        $this->session->sess_destroy();
        redirect("login");
    }
}