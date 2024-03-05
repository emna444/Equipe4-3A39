<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AccessDeniedController extends AbstractController
{
    public function accessDenied(): Response
    {
        return $this->render('access_denied.html.twig');
    }
}