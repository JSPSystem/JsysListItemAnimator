<?php

namespace Plugin\JsysListItemAnimator;

use Eccube\Plugin\AbstractPluginManager;
use Plugin\JsysListItemAnimator\Entity\Config;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PluginManager.
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * @var string
     */
    private $plugin_code = 'JsysListItemAnimator';

    public function enable(array $meta, ContainerInterface $container)
    {
        // プラグイン設定が未登録の場合は、初期データを登録
        $em     = $container->get('doctrine.orm.entity_manager');
        $Config = $em->find(Config::class, 1);
        if ($Config) {
            return;
        }

        $Config = new Config();
        $Config
            ->setOptionHideBeforeAnimation(false)
            ->setOptionSlideAnimation(false)
            ->setOptionStagger(false)
            ->setOptionScrollTrigger(false)
            ->setOptionScrollTriggerMarkers(false)
            ->setOptionChangeScaleOnHover(false);
        $em->persist($Config);
        $em->flush($Config);
    }

    /**
     * @param array $meta
     * @param ContainerInterface $container
     * @return void
     */
    public function uninstall(array $meta, ContainerInterface $container)
    {
        // PluginDataにあるディレクトリごとファイルを削除
        $dir = $container->getParameter('plugin_data_realdir');
        $fs  = new Filesystem();
        $fs->remove($dir . '/' . $this->plugin_code);
    }

}
