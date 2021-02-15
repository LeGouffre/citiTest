<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/addUser", name="app_addUser_")
 */
class UserProjectController extends AbstractController
{
    private $_em;
    private $_sendPasswd = [];
    private $_encoder;
    private $_mailer;
    /**
     * @Route("", name="getPage", methods={"GET"})
     */
    public function getPage()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return ($this->render("user_project/index.html.twig", []));
    }
    /**
     * @Route("", name="postUser", methods={"POST"})
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //upload a csv with email | fName | Lname | location
        if (!isset($request->files->all()["usersFile"])) {

            return ($this->render("user_project/index.html.twig", ["errors" => ["aucun fichier trouver"]]));
        }
        $this->_encoder = $encoder;
        $fileInfo = $request->files->all()["usersFile"];
        $data = file_get_contents($fileInfo->getRealPath());
        $data = explode("\n", $data);
        $tmp = [];
        $c = -1;
        $flag_upl = false;
        $len = count($data);
        $location = "";
        $erros = [];
        $this->_em = $this->getDoctrine()->getManager();
        if ($data[$len - 1] === "")
            unset($data[--$len]);
        while (++$c < $len) {
            $tmp = explode(";", $data[$c]);
            if (count($tmp) === 1)
                $tmp = explode(",", $data[$c]);
            if ($c === 0 && in_array("email", $tmp))
                $c++;
            else if (count($tmp) < 4 && $flag_upl === false);
            else {
                if ($flag_upl === false) {
                    $location = $tmp[count($tmp) - 1];
                    $flag_upl = true;
                } else if ($flag_upl === true && ($err = $this->checkEmail($tmp[0], $c)) !== true) {
                    $erros[] = $err;
                } else if ($flag_upl === true) {
                    $this->uploadUser(
                        ["email" => $tmp[0], "firstName" => $tmp[1], "lastName" => $tmp[2]],
                        $location
                    );
                    $this->_em->flush();
                }
            }
        }
        if (count($erros) > 0) {
            return ($this->render("user_project/index.html.twig", ["errors" => $erros]));
        }
        return ($this->sendEmail($location));
    }

    /**
     * @Route("/changePasswd", name="changePassworduser", methods={"POST"})
     */
    public function changePasswd(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $passwd = $request->get("password");
        $passwdConf = $request->get("confirm_password");
        if ($passwd === null || $passwdConf === null || $passwdConf === "" || $passwd === "" || $passwdConf !== $passwd)
            return ($this->render("user_project/activateUser.html.twig",["errorPasswd" => "Les mots de passe ne corresponde pas."]));
        else if (!$user instanceof User)
            return ($this->redirectToRoute("app_login"));
        $user->setPassword($encoder->encodePassword($user, $passwd));
        $user->setTokenRegistration(null);
        $user->setActivate(true);
        $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
        $this->get("security.token_storage")->setToken($token);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        return($this->redirectToRoute("home_app_home"));
    }
    /**
     * @Route("/{token}", name="activeUser", methods={"GET"})
     */
    public function activeUser($token, Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(["tokenRegistration" => $token]);
        if ($user instanceof User)
        {
            if ($user->getActivate() === true)
                return $this->render("user_project/activateUser.html.twig", ["error" => "Cet utilisateur a deja ete activer."]);
            $user->setActivate(true);
            $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);

            return ($this->render("user_project/activateUser.html.twig", ["success" => "votre compte a bien ete activer."]));
        }
        else
        {
            return $this->render("user_project/activateUser.html.twig", ["error" => "Aucun compte n'a ete trouver."]);
        }
    }


    private function checkEmail($name, $c)
    {
        if (!filter_var($name, FILTER_VALIDATE_EMAIL)) {
            return ("Email invalide : " . $name . " ligne " . ($c + 1));
        }
        return true;
    }

    private function sendEmail($location)
    {
        $c = -1;
        $len = count($this->_sendPasswd);
        $fail = [];
        while (++$c < $len) {
            $message = (new \Swift_Message("Ajout a la campagne de depistage " . $location))
                ->setFrom("cititest.contact@gmail.com")
                ->setTo($this->_sendPasswd[$c]['email'])
                ->setBody(
                    $this->renderView(
                        "email/addTocamp.html.twig",
                        [
                            "user" => $this->_sendPasswd[$c],
                            "location" => $location,
                        ]
                    ),
                    "text/html"
                )
;
            $transporter = (new \Swift_SmtpTransport('smtp.gmail.com', 465, "ssl"))
                ->setUsername('cititest.contact@gmail.com')
                ->setPassword('ginsu7510');
            $this->_mailer = new \Swift_Mailer($transporter);
            if (!$this->_mailer->send($message, $fail)) {
                $errors = ["Une erreur a été rencontrés lors de l'envoie du mail à destination de " . $this->_sendPasswd[$c]["email"]];
                $this->removeUser($this->_sendPasswd[$c]);
                return ($this->render("user_project/index.html.twig", ["errors" => $errors]));
            }
        }
         return ($this->render("user_project/index.html.twig", ["success" => "utilisateur ajouté à la campagne " . $location]));
    }


    private function uploadUser($users, $location)
    {
        $userRep = $this->getDoctrine()->getManager()->getRepository(User::class);
        if (($user = $userRep->checkUserExist($users)) instanceof User) {
            $user->setLocation($location);
            $this->_em->persist($user);
            $this->_sendPasswd[] = [
                "email" => $users["email"],
                "passwd" => "",
                "userName" => $user->getUsername()

            ];
        } else {
            $user = $this->createUser($location, $users);
            $this->_em->persist($user);
        }
    }

    private function createUser($location, $usert)
    {
        $user = new User();
        $user->setLocation($location);
        $user->setEmail($usert["email"]);
        $user->setUserName($this->makeUserName($usert));
        $passwd = $this->getRandPasswd(8);
        $user->setTokenRegistration($this->getRandPasswd(20));
        $this->_sendPasswd[] = [
            "email" => $usert["email"], "passwd" => $passwd,
            "token" => $user->getTokenRegistration(),
            "userName" => $user->getUsername(),
        ];
        $user->setPassword($this->_encoder->encodePassword($user, $passwd));
        return ($user);
    }
    private function removeUser($data) {
        $urep = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $urep->findOneBy(['email' => $data['email']]);
        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
    }


    private function makeUserName($user)
    {

        $uname = $user["lastName"][0] . "." . $user["firstName"];
        $userRep = $this->getDoctrine()->getRepository(User::class);
        $uTmp = $userRep->findBy(["userName" => $uname]);
        if (empty($uTmp))
            return ($uname);
        return ($uname . count($uTmp));
    }

    private function getRandPasswd($lim)
    {
        $c = -1;
        $range = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
        $str = "";
        while (++$c < $lim) {
            $str .= $range[rand(0, strlen($range) - 1)];
        }
        return ($str);
    }
}
