<?php

namespace App\Controller;

use App\Entity\TokenNumber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode;

/**
 * @Route("/generator", name="ticket_generator")
 */

class TicketGeneratorController extends AbstractController
{
    private $_badToken = [];
    /**
     * @Route("", name="_get_form", methods={"GET"})
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        return $this->render("ticket_generator/index.html.twig", []);
    }

    /**
     * @Route("/{nb}", name="get_token_file",)
     */
    public function GenerateFile($nb)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $nb = intval($nb);
        if ($nb < 1 || $nb > 1000)
            return ($this->render("ticket_generator/index.html.twig", ["error" => "Veuillez entrer une valeur correct."]));
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(TokenNumber::class);
        $token = $repo->findBy(["isUsed" => false]);
        $c = -1;
        if (($len = count($token)) < $nb)
            $this->generateToken($token, $em, $repo,$nb - $len);
        $len = count($token);
        while (++$c < $nb) {
            $qrCode = new QrCode('https://citi-test.xyz/result/' . $token[$c]->getNumber());
            $result[] = ["number" => $token[$c]->getNumber(), 'qrcode' => $qrCode->writeDataUri()];
        }
        return $this->render('ticket_generator/indexPDF.html.twig', [
            'tokens' => $result
        ]);
    }

    private function generateToken(&$tokens, &$em, &$repo, $nb)
    {
        $batch = 500;
        $c = -1;
        while (++$c < $nb) {
            $token = new TokenNumber();
            $token->setNumber((int) $this->generateTokenNumber());
            while ($this->checkToken($token->getNumber(), $repo) === false) {
                $token->setNumber($this->generateTokenNumber());
            }
            $em->persist($token);
            $tokens[] = $token;
            if ($c % $batch === 0) {
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
    }

    private function generateTokenNumber()
    {
        $c = -1;
        $str = "";
        while (++$c < 8) {
            if ($c === 0)
                $num = rand(1, 9);
            else
                $num = rand(0, 9);
            $str .= (string) $num;
        }
        return $str;
    }

    private function checkToken($token, &$repo)
    {
        if (in_array((int) $token, $this->_badToken))
            return false;
        else if ($repo->findOneBy(["number" => (int) $token]) instanceof TokenNumber) {
            $this->_badToken[] = $token;
            return false;
        }
        else if (strlen($token) !== 8)
            return false;
        return true;
    }
}
