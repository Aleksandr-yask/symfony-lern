<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Messages\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ConferenceController extends AbstractController
{
    private Environment $twig;
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $bus;

    public function __construct(Environment $twig,
                                EntityManagerInterface $entityManager,
                                MessageBusInterface $bus)
    {
        $this->twig = $twig;
        $this->bus = $bus;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/")
    */
     public function indexNoLocale()
    {
        return $this->redirectToRoute('homepage', ['_locale' => 'en']);
    }


    /**
     * @Route("/{_locale<%app.supported_locales%>}/", name="homepage")
     *
     * @param ConferenceRepository $conferenceRepository
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        $response = new Response($this->twig->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll()
        ]));
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/conference/header", name="conference_header")
     * @param ConferenceRepository $conferenceRepository
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function conferenceHeader(ConferenceRepository $conferenceRepository)
    {
        $response = new Response($this->twig->render('conference/header.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));
        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/conference/{slug}", name="conference")
     *
     * @param Request $request
     * @param Conference $conference
     * @param CommentRepository $commentRepository
     * @param string $photoDir
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Request $request,
                         Conference $conference,
                         CommentRepository $commentRepository,
                         string $photoDir)
    {
        //$request->getLocale();
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $comment->setConference($conference);
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6) . '.'. $photo->guessExtension());
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {

                }
                $comment->setPhotoFilename($filename);
            }

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $context = [
              'user_ip' => $request->getClientIp(),
              'user_agent' => $request->headers->get('user-agent'),
              'referrer' => $request->headers->get('referer'),
              'permalink' => $request->getUri()
            ];

            $this->bus->dispatch(new CommentMessage($comment->getId(), $context));


            $this->entityManager->flush();

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPagination($conference, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form->createView()
        ]));
    }



}
