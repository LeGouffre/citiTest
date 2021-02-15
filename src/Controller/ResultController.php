<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\TokenNumber;
use Symfony\Component\HttpFoundation\Response;
/**
* @Route("/result", name="result")
*/
class ResultController extends AbstractController
{
    /**
     * @Route("", name="Get_result")
     */
    public function get_result()
    {
        return $this->render('result/index.html.twig', [
            'controller_name' => 'ResultController',
        ]);
    }
    /**
     * @Route("/{id}", name="Show_result")
     */
    public function show_result($id)
    {
        $resRep = $this->get("doctrine")->getRepository(TokenNumber::class);
        $result = $resRep->findOneBy(["number" => (int) $id, "active" => true]);
        if ($result instanceof TokenNumber)
        {
            $result->setHasSee(true);
            $result->setSeeAt(new \DateTime('now'));
            return $this->render('result/index.html.twig', [
                'card' => $result,
            ]);
        }
        else
        {
            return $this->render('result/index.html.twig', [
                'errors' => 'Numéro non définie', "card" => ["number" => $id]]);
        }
    }
}
