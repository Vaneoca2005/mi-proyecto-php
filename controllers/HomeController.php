<?php
include("secure_area.php");
class HomeController extends secure_area {
    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->load->view("home");
    }
}
