<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{

    public function indexAction($page)
    {
        if ($page < 1)
        {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }

        $nbPerPage = 3;

        $listAdverts = $this->getDoctrine()
                ->getManager()
                ->getRepository('OCPlatformBundle:Advert')
                ->getAdverts($page, $nbPerPage);

        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        if ($page > $nbPages)
        {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
        }

        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
                    'listAdverts' => $listAdverts,
                    'nbPages' => $nbPages,
                    'page' => $page,
        ));
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em
                ->getRepository('OCPlatformBundle:Advert')
                ->myFindOne($id)
        ;

        if (null === $advert)
        {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        $listApplications = $em
                ->getRepository('OCPlatformBundle:Application')
                ->findBy(array('advert' => $advert))
        ;

        $listAdvertSkills = $em
                ->getRepository('OCPlatformBundle:AdvertSkill')
                ->findBy(array('advert' => $advert))
        ;

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
                    'advert' => $advert,
                    'listApplications' => $listApplications,
                    'listAdvertSkills' => $listAdvertSkills
        ));
    }

    public function addAction(Request $request)
    {
        if ($request->isMethod('POST'))
        {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }

//        $advert = new Advert();
//        $advert->setTitle('Recherche développeur Symfony.');
//        $advert->setAuthor('Alexandre');
//        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
//
//        $image = new Image();
//        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
//        $image->setAlt('Job de rêve');
//
//        $advert->setImage($image);
//
//        $em = $this->getDoctrine()->getManager();
//
//        $em->persist($advert);
//        $advert = new Advert();
//        $advert->setTitle('Recherche développeur Symfony.');
//        $advert->setAuthor('Alexandre');
//        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
//
//        $application1 = new Application();
//        $application1->setAuthor('Marine');
//        $application1->setContent("J'ai toutes les qualités requises.");
//        $application1->setDate(new \DateTime());
//
//        $application2 = new Application();
//        $application2->setAuthor('Pierre');
//        $application2->setContent("Je suis très motivé.");
//        $application2->setDate(new \DateTime());
//
//        $application1->setAdvert($advert);
//        $application2->setAdvert($advert);
//
//        $em = $this->getDoctrine()->getManager();
//
//        $em->persist($advert);
//
//        $em->persist($application1);
//        $em->persist($application2);
//

        $em = $this->getDoctrine()->getManager();

        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

        $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();

        foreach ($listSkills as $skill)
        {
            $advertSkill = new AdvertSkill();

            $advertSkill->setAdvert($advert);
            $advertSkill->setSkill($skill);
            $advertSkill->setLevel('Expert');

            $em->persist($advertSkill);
        }

        $em->persist($advert);

        $em->flush();
        return $this->render('OCPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        if ($request->isMethod('POST'))
        {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }

        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert)
        {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

        foreach ($listCategories as $category)
        {
            $advert->addCategory($category);
        }

        $em->flush();


        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
                    'advert' => $advert
        ));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert)
        {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        foreach ($advert->getCategories() as $category)
        {
            $advert->removeCategory($category);
        }

        $em->flush();

        return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
                array(), array('date' => 'desc'), $limit, 0
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
                    'listAdverts' => $listAdverts
        ));
    }

}
