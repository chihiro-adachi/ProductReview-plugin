<?php
/**
 * This file is part of the ProductReview plugin.
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductReview\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Eccube\Application;
use Plugin\ProductReview\Form\Type\Admin\ProductReviewConfigType;
use Plugin\ProductReview\Repository\ProductReviewConfigRepository;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ConfigController.
 */
class ConfigController extends AbstractController
{
    private $reviewConfigRepository;

    private $logger;

    public function __construct(
        ProductReviewConfigRepository $productReviewConfigRepository,
        LoggerInterface $logger
    ) {
        $this->reviewConfigRepository = $productReviewConfigRepository;
        $this->logger = $logger;
    }

    /**
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     *
     * @Route("/{_admin}/plugin/product/review/config", name="plugin_ProductReview_config")
     */
    public function index(Request $request)
    {
        $config = $this->reviewConfigRepository->find(1);
        if (!$config) {
            throw new NotFoundHttpException();
        }

        /* @var $form FormInterface */
        $form = $this
            ->createFormBuilder(ProductReviewConfigType::class, $config)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /* @var $em EntityManager */
                $em = $this->getDoctrine()->getManager();
                $em->persist($config);
                $em->flush($config);

                $this->logger->info('Product review config', array('status' => 'Success'));

                $this->addFlash('success', 'plugin.admin.product_review_config.save.complete');
            } catch (\Exception $e) {
                $this->logger->info('Product review config', array('status' => $e->getMessage()));

                $this->addFlash('error', 'plugin.admin.product_review_config.save.error');
            }
        }

        return $this->render('ProductReview/Resource/template/admin/config.twig', array('form' => $form->createView()));
    }
}
