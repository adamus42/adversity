<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\PaginatotBundle\KnpPaginatorBundle ;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\PhotoType;
use App\Entity\Photo;


class BlogController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     * WebPage displaying the photos of the album
     */
    public function index(): Response
    {
        $photos = $this->getDoctrine()->getRepository('App:Photo')->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'photos' => $photos,
        ]);
    }

     /**
     * @Route("/upload", name="upload")
     * Web page that uploads the photos of the album 
     */
     public function upload(Request $request): Response
     {
        $photo = new Photo();
        $formUpload =  $this->createForm(PhotoType::class, $photo);

        $formUpload->handleRequest($request);
        if ($formUpload->isSubmitted() && $formUpload->isValid()) {
            $photo = $formUpload->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $photo->setSlug($photo->getTitle());
            $entityManager->persist($photo);
            $entityManager->flush();

            return $this->redirectToRoute('upload');
        }

        return $this->render('blog/upload.html.twig', [
            'controller_name' => 'BlogController',
            'formUpload' => $formUpload->createView(),
        ]);
    }


     /**
     * @Route("/{slug}", name="photo", methods={"GET"})
     * Viewing a photo
     */
     public function show(Photo $photo): Response
     {
        return $this->render('blog/photo.html.twig', [
            'photo' => $photo,
        ]);
    }




    /**
     * @Route("/deletePhoto", name="deletePhoto", methods={"POST"})
     * deleting a photo
     */
    public function deletePhoto(Request $request): Response
    {
        if ($request->getMethod() == "POST") {

          $idPhoto = $request->request->get('idPhoto');
          $photo = $this->getDoctrine()->getRepository('App:Photo')->find($idPhoto);

          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->remove($photo);
          $entityManager->flush();

          return $this->redirectToRoute('index');
      }



  }
}
