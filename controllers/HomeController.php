<?php

namespace AmichiamociAppApp\Controllers;
use PhpMvc\Controller;


class HomeController extends Controller {

    public function index() {
        return $this->content('Hello, world!');

        // create to the ./view/home/index.php
        // and use view function to return this view:
        // return $this->view();
    }

}