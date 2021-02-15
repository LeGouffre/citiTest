<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Endroid\QrCode\QrCode;
    /**
     * @Route("", name="home")
     */
class HomeController extends AbstractController
{
    /**
     * @Route("", name="_app_home")
     */
    public function index()
    {
        if ($this->getUser() instanceof User)
        {
            return ($this->render("init_token/index.html.twig", []));
        }
        else
        {
            return ($this->render("result/index.html.twig"));
        }
    }
}
