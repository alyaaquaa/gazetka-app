<?php
/**
 * ArticleController.
 */

namespace App\Controller;

use App\Entity\Article;
use App\Form\Type\ArticleType;
use App\Service\ArticleServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ArticleController.
 */
#[Route('/article')]
class ArticleController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param ArticleServiceInterface $articleService Article service
     * @param TranslatorInterface     $translator     Translator
     */
    public function __construct(private readonly ArticleServiceInterface $articleService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'article_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $pagination = $this->articleService->getAdminPaginatedList($page);
        } else {
            $pagination = $this->articleService->getPaginatedList($page, $this->getUser());
        }

        return $this->render('article/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Article $article Article entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'article_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Article $article): Response
    {
        return $this->render(
            'article/show.html.twig',
            ['article' => $article]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'article_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $article = new Article();
        $article->setAuthor($user);
        $form = $this->createForm(
            ArticleType::class,
            $article,
            ['action' => $this->generateUrl('article_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->save($article);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Article $article Article entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'article_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(
            ArticleType::class,
            $article,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('article_edit', ['id' => $article->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->save($article);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/edit.html.twig',
            [
                'form' => $form->createView(),
                'article' => $article,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Article $article Article entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'article_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Article $article): Response
    {
        $form = $this->createForm(
            FormType::class,
            $article,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('article_delete', ['id' => $article->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->delete($article);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/delete.html.twig',
            [
                'form' => $form->createView(),
                'article' => $article,
            ]
        );
    }
}
