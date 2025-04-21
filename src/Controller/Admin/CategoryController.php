<?php

namespace App\Controller\Admin;

use App\Entity\IssueCategory;
use App\Form\Admin\IssueCategoryCreatedType;
use App\Model\Admin\IssueCategoryCreated;
use App\Repository\IssueCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryController extends AbstractController
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/category', name: 'app_admin_category_index')]
    public function index(IssueCategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IssueCategoryCreatedType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var IssueCategoryCreated $issueCategoryCreated */
            $issueCategoryCreated = $form->getData();

            $category = IssueCategory::createFromIssueCategoryCreated($issueCategoryCreated);
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('admin.category.created'));
            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/category/new.html.twig', [
            'form' => $form,
        ]);
    }

}
