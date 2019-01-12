<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DebugController extends AbstractController {
    /**
    * @Route("/", name="_debug_welcome", options={"sitemap" = true})
    */
    public function indexAction()
    {
        return $this->render("debug/index.html.twig");
    }
}
