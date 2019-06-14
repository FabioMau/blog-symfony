<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Form\PostType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BlogController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1"}, methods={"GET"}, name="index")
     */
    public function index(Request $request, PostRepository $posts, CommentRepository $comments): Response
    {

        $query = $request->query->get('q');
        $datele = $request->query->get('datele');
        $datege = $request->query->get('datege');
        if ($query !== null || $datele !== null || $datege !== null) {
            $latestPosts = $posts->findByFilters($query, $datele, $datege);
        } else {
            $latestPosts = $posts->findBy(array(), array('createdAt' => 'DESC'));
        }

        return $this->render('index.html.twig', [
            'posts' => $latestPosts,
            'query' => $query,
            'datele' => $datele,
            'datege' => $datege,
        ]);
    }
    /**
     * @Route("/post/{id<[0-9]\d*>}", methods={"GET"}, name="post_show")
     */
    public function getPost(int $id, PostRepository $posts, AuthorizationCheckerInterface $authChecker)
    {
        $post = $posts->findOneBy(array('id' => $id));

        return $this->render('post.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/post/new", methods={"GET","POST"}, name="post_new")
     */
    public function newPost(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Accedi prima!');

        $post = new Post();
        $user = $this->getUser();
        $post->setCreatedBy($user);
        $post->setCreatedAt(new \DateTime());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->render('success.html.twig', [
                'message' => "Post creato con successo!",
            ]);
        }

        return $this->render('post/form.html.twig', [
            'title' => "Crea nuovo post",
            'PostForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id<[0-9]\d*>}/edit", methods={"GET","POST"}, name="post_edit")
     */
    public function editPost(Request $request, int $id, PostRepository $posts, AuthorizationCheckerInterface $authChecker): Response
    {
        $post = $posts->findOneBy(array('id' => $id));

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Accedi prima!');
        if (
            $this->getUser() !== $post->getCreatedBy()
            && false === $authChecker->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Non puoi modificare i post altrui.');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->render('success.html.twig', [
                'message' => "Post modificato con successo!",
            ]);
        }

        return $this->render('post/form.html.twig', [
            'title' => "Modifica post",
            'PostForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id<[0-9]\d*>}/delete", methods={"GET"}, name="post_delete")
     */
    public function deletePost(int $id, PostRepository $posts, AuthorizationCheckerInterface $authChecker): Response
    {
        $post = $posts->findOneBy(array('id' => $id));

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Accedi prima!');
        if (
            $this->getUser() !== $post->getCreatedBy()
            && false === $authChecker->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Non puoi modificare i post altrui.');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->render('success.html.twig', [
            'message' => "Post eliminato con successo!",
        ]);
    }

    /**
     * @Route("/post/{id<[0-9]\d*>}/comment/new", methods={"GET","POST"}, name="comment_new")
     */
    public function newComment(int $id, PostRepository $posts, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Accedi prima!');

        $comment = new Comment();
        $comment->setCreatedAt(new \DateTime());
        $post = $posts->findOneBy(array('id' => $id));
        $comment->setPost($post);
        $user = $this->getUser();
        $comment->setCreatedBy($user);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('post_show', array('id' => $id,)));
        }

        return $this->render('post/comment.html.twig', [
            'title' => "Crea nuovo commento",
            'CommentForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id<[0-9]\d*>}/comment/{idc<[0-9]\d*>}/edit", methods={"GET","POST"}, name="comment_edit")
     */
    public function editComment(Request $request, int $id, int $idc, CommentRepository $comments, AuthorizationCheckerInterface $authChecker): Response
    {
        $comment = $comments->findOneBy(array('id' => $idc));

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Accedi prima!');
        if (
            $this->getUser() !== $comment->getCreatedBy()
            && false === $authChecker->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Non puoi modificare i post altrui.');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('post_show', array('id' => $id,)));
        }

        return $this->render('post/comment.html.twig', [
            'title' => "Modifica commento",
            'CommentForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id<[0-9]\d*>}/comment/{idc<[0-9]\d*>}/delete", methods={"GET"}, name="comment_delete")
     */
    public function deleteComment(int $id, int $idc, CommentRepository $comments, AuthorizationCheckerInterface $authChecker): Response
    {
        $comment = $comments->findOneBy(array('id' => $idc));

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Accedi prima!');
        if (
            $this->getUser() !== $comment->getCreatedBy()
            && false === $authChecker->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Non puoi modificare i post altrui.');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('post_show', array('id' => $id,)));
    }
}
