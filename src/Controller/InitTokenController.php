<?php

namespace App\Controller;

use App\Entity\TokenNumber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

/**
 * @Route("/init", name="token_")
 */
class InitTokenController extends AbstractController
{
    /**
     * @Route("", name="init_token", methods={"GET"})
     */
    public function index()
    {
        return $this->render('init_token/index.html.twig', []);
    }

    /**
     * @Route("", name="post_token", methods={"POST"})
     */
    public function ActiveToken(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $repo = $this->getDoctrine()->getManager()->getRepository(TokenNumber::class);
        $token = $repo->findOneBy(["number" => (int) $data['data']['num']]);
        if ($token instanceOf TokenNumber and $token->getActive() === false)
        {
            $token->setActive(true);
            if ($this->getUser()->getLocation() === null)
                return $this->json(
                   ['errors' => ["Aucune adresse est enregistrer sur votre compte, merci de contacter votre référent ou le support par mail a cititest.contact@gmail.com"],
                   'cardNumber' => $token->getNumber()], 403);
            $token->setLocation($this->getUser()->getLocation());
            $token->setUpdateAt(new DateTime('now'));
            $token->setActivateBy($this->getUser());
            $this->getDoctrine()->getManager()->persist($token);
            $this->getDoctrine()->getManager()->flush();
            return $this->json([]);
        }
        if ($token instanceof TokenNumber)
            return ($this->json(["errors" => ["La carte: " . $data['data']['num'] . " déjà activée."]], 403));
        return ($this->json(["Aucune carte n'a été trouvé avec le numéro " . $data['data']['num'] . '.'], 403));
    }
}
