<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TokenNumberRepository;
use App\Entity\TokenNumber;

/**
     * @Route("/card", name="card")
     */
class TokenNumberController extends AbstractController
{
    /**
     * @Route("", name="_getCard", methods={"GET"})
     */
    public function get_card()
    {
        return $this->render('token_number/index.html.twig', [
            'controller_name' => 'TokenNumberController',
        ]);
    }
    /**
     * @Route("", name="_updateCard", methods={"POST"})
     */
    public function update_card(Request $request, TokenNumberRepository $card)
    {
        $target = $card->findOneBy(
            [
                "number" => (int) $request->request->get("cardNumber"),
                "result" => null,
                "active" => true,
				"hasSee" => false
            ]  );
        $errors = [];
        $success = "";
        $result = $request->request->get("resultOptions");
        if ($target instanceof TokenNumber && ($result === "yes" || $result === "no" || $result === "undefined"))
        {
            $target->setResult($result);
            $target->setTestedBy($this->getUser());
            $target->setTestedAt(new \DateTime('now'));
            if ($this->getUser()->getLocation() === null)
                return $this->render('token_number/index.html.twig',
                   ['errors' => ["Aucune adresse est enregistrer sur votre compte, merci de contacter votre référent ou le support par mail a cititest.contact@gmail.com"],
                   'cardNumber' => $request->request->get("cardNumber")]);
            $target->setLocation($this->getUser()->getLocation());
            $em = $this->getDoctrine()->getManager();
            $em->persist($target);
            $em->flush();
            $success = "Carte Modifier";
            return $this->render('token_number/index.html.twig',
                ['success' => $success]);
        }
        else
        {
            if ($result !== "yes" && $result !== "no" && $result === "undefined")
                $errors['resultOptions'] = "Resultat invalide.";
            if (!($target instanceof TokenNumber))
                $errors['cardNumber'] = "Numero non valide ou carte déjà activée.";
            return $this->render('token_number/index.html.twig',
                ['errors' => $errors, 'cardNumber' => $request->request->get("cardNumber")]);
        }
    }
}
