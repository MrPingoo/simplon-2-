<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class EmailService
{
    protected $mailer;
    private $templating;
    private $em;

    public function __construct(\Swift_Mailer $mailer,\Twig_Environment $templating,EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->em = $em;
    }

    public function sendResetPwd($subject, $from, $to, $args) {
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->renderFactureTemplate($args),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    public function renderFactureTemplate($args)
    {
        return $this->templating->render(
            'email/reset_pwd.html.twig',
            $args
        );
    }
}