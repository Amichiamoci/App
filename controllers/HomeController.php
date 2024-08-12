<?php
// make sure you add the Controllers child name to the root namespace of your application
namespace AmichiamociAppApp\Controllers;

// import the base class of the controller
use PhpMvc\Controller;

// expand your class with the base controller class
class HomeController extends Controller {

    public function index() {
        // use the content function to return text content:
        return $this->content('Hello, world!');

        // create to the ./view/home/index.php
        // and use view function to return this view:
        // return $this->view();
    }

}