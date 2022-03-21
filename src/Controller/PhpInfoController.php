<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

class PhpInfoController extends AbstractController
{
    #[Route('/phpinfo', name: 'app_phpinfo')]
    public function index(): Response
    {

        ob_start();
        phpinfo();
        $str = ob_get_contents();
        ob_get_clean();

        return new Response($str);
    }
}
