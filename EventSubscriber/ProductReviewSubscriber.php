<?php
/**
 * This file is part of the ProductReview plugin.
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductReview\EventSubscriber;

use Eccube\Application;
use Eccube\Event\TemplateEvent;
use Plugin\ProductReview\Util\Version;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class Event.
 */
class ProductReviewSubscriber implements EventSubscriberInterface // 自動的にeventdispatcherに登録される
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Product/detail.twig' => 'onProductDetailRender',
            'eccube.event.render.product_detail.before' => 'onRenderProductsDetailBefore'
        ];
    }

    /**
     * TODO コンストラクタインジェクションに移行
     *
     * @var Application
     */
    private $app;

    /**
     * MakerEvent constructor.
     *
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param TemplateEvent $event
     */
    public function onProductDetailRender(TemplateEvent $event)
    {
        $this->app['product_review.event.product_review']->onProductDetailRender($event);
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onRenderProductsDetailBefore(FilterResponseEvent $event)
    {
        if ($this->supportNewHookPoint()) {
            return;
        }

        $this->app['product_review.event.product_review_legacy']->onRenderProductsDetailBefore($event);
    }

    /**
     * @return bool v3.0.9以降のフックポイントに対応しているか？
     */
    private function supportNewHookPoint()
    {
        return Version::isSupportVersion();
    }
}
