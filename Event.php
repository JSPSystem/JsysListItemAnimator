<?php

namespace Plugin\JsysListItemAnimator;

use Eccube\Common\EccubeConfig;
use Eccube\Event\TemplateEvent;
use Plugin\JsysListItemAnimator\Service\GsapTwigService as Gsap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Event implements EventSubscriberInterface
{
    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    /**
     * Event constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Product/list.twig' => 'onRenderProductList',
        ];
    }

    /**
     * 商品一覧Twig
     *
     * @param TemplateEvent $event
     * @return void
     */
    public function onRenderProductList(TemplateEvent $event)
    {
        $path = Gsap::getTwigDirPath($this->eccubeConfig) . Gsap::FNAME_TWIG;
        if (file_exists($path)) {
            $contents = file_get_contents($path);
            $event->addAsset($contents, false);
        }
    }

}
