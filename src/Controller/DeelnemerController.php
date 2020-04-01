<?php

namespace App\Controller;


use App\Form\PasswordUpdateType;
use App\Form\UserUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DeelnemerController extends AbstractController
{
    /**
     * @Route("/user/activiteiten", name="activiteiten")
     */
    public function activiteitenAction()
    {
        $usr= $this->get('security.token_storage')->getToken()->getUser();

        $beschikbareActiviteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
        ->getBeschikbareActiviteiten($usr->getId());

        $ingeschrevenActiviteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->getIngeschrevenActiviteiten($usr->getId());

        $totaal=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->getTotaal($ingeschrevenActiviteiten);


        return $this->render('deelnemer/activiteiten.html.twig', [
                'beschikbare_activiteiten'=>$beschikbareActiviteiten,
                'ingeschreven_activiteiten'=>$ingeschrevenActiviteiten,
                'totaal'=>$totaal,
        ]);
    }

    /**
     * @Route("/user/inschrijven/{id}", name="inschrijven")
     */
    public function inschrijvenActiviteitAction($id)
    {
        $now = new \DateTime();
        $activiteit = $this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->find($id);
        if ($activiteit->getMaxDeelnemers() == $activiteit->getUsers()->count()) {
            $this->addFlash('error', 'deelnemers limiet bereikt!');
        } else if ($activiteit->getDatum()->getTimestamp() < $now->getTimestamp()) {
            $this->addFlash('error', 'deadline bereikt!');
        } else {
            $usr = $this->get('security.token_storage')->getToken()->getUser();
            $usr->addActiviteit($activiteit);

            $em = $this->getDoctrine()->getManager();
            $em->persist($usr);
            $em->flush();
        }

        return $this->redirectToRoute('activiteiten');
    }

    /**
     * @Route("/user/uitschrijven/{id}", name="uitschrijven")
     */
    public function uitschrijvenActiviteitAction($id)
    {
        $activiteit = $this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->find($id);
        $usr= $this->get('security.token_storage')->getToken()->getUser();
        $usr->removeActiviteit($activiteit);
        $em = $this->getDoctrine()->getManager();
        $em->persist($usr);
        $em->flush();
        return $this->redirectToRoute('activiteiten');
    }

    /**
     * @Route("/user/profiel", name="edit_profile")
     */
    public function editProfile(UserInterface $user, Request $request)
    {
        $user->setPlainPassword('a');
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->add('save', SubmitType::class, ['label' => 'wijzig']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'gegevens gewijzigd');
            return $this->redirectToRoute('activiteiten');
        }

        return $this->render('deelnemer/profiel.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/wachtwoord", name="edit_password")
     */
    public function editPassword(UserInterface $user, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $form = $this->createForm(PasswordUpdateType::class, $user);
        $form->add('save', SubmitType::class, ['label' => 'wijzig']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Wachtwoord gewijzigd');
            return $this->redirectToRoute('activiteiten');
        }

        return $this->render('deelnemer/wachtwoord.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
