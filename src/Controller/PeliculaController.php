<?php

namespace App\Controller;

use App\Entity\Pelicula;
use App\Form\PeliculaType;
use App\Repository\PeliculaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pelicula')]
final class PeliculaController extends AbstractController
{
    #[Route(name: 'app_pelicula_index', methods: ['GET'])]
    public function index(PeliculaRepository $peliculaRepository): Response
    {
        return $this->render('pelicula/index.html.twig', [
            'peliculas' => $peliculaRepository->findAll(),
        ]);
    }

#[Route('/new', name: 'app_pelicula_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $pelicula = new Pelicula();
    $form = $this->createForm(PeliculaType::class, $pelicula);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Manejar archivo
        $imagenFile = $form->get('imagen')->getData();
        if ($imagenFile) {
            $nuevoNombre = uniqid().'.'.$imagenFile->guessExtension();
            $imagenFile->move(
                $this->getParameter('imagenes_directory'),
                $nuevoNombre
            );
            $pelicula->setImagen($nuevoNombre);
        }

        $entityManager->persist($pelicula);
        $entityManager->flush();

        return $this->redirectToRoute('app_pelicula_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('pelicula/new.html.twig', [
        'pelicula' => $pelicula,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_pelicula_show', methods: ['GET'])]
    public function show(Pelicula $pelicula): Response
    {
        return $this->render('pelicula/show.html.twig', [
            'pelicula' => $pelicula,
        ]);
    }

#[Route('/{id}/edit', name: 'app_pelicula_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Pelicula $pelicula, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(PeliculaType::class, $pelicula);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imagenFile = $form->get('imagen')->getData();
        if ($imagenFile) {
            $nuevoNombre = uniqid().'.'.$imagenFile->guessExtension();
            $imagenFile->move(
                $this->getParameter('imagenes_directory'),
                $nuevoNombre
            );
            $pelicula->setImagen($nuevoNombre);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_pelicula_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('pelicula/edit.html.twig', [
        'pelicula' => $pelicula,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_pelicula_delete', methods: ['POST'])]
    public function delete(Request $request, Pelicula $pelicula, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pelicula->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pelicula);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pelicula_index', [], Response::HTTP_SEE_OTHER);
    }
}
