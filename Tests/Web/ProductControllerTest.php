<?php

namespace Plugin\JsysListItemAnimator\Tests\Web;

use Eccube\Tests\Web\AbstractWebTestCase;
use Plugin\JsysListItemAnimator\Entity\Config;
use Plugin\JsysListItemAnimator\Repository\ConfigRepository;
use Plugin\JsysListItemAnimator\Service\GsapTwigService as Gsap;
use Symfony\Component\Filesystem\Filesystem;

class ProductControllerTest extends AbstractWebTestCase
{
    /**
     * @var ConfigRepository
     */
    protected $configRepo;

    /**
     * @var Gsap
     */
    protected $gsapService;

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->configRepo  = static::getContainer()->get(ConfigRepository::class);
        $this->gsapService = static::getContainer()->get(Gsap::class);
    }

    /**
     * 商品一覧でアニメーション用ファイルが読み込めているか
     *
     * @return void
     */
    public function testReadAnimationFile()
    {
        // すでにアニメーションファイルが存在する場合はリネームして退避
        $file   = Gsap::getTwigDirPath($this->eccubeConfig) . Gsap::FNAME_TWIG;
        $bak    = $file . '.' . date('YmdHis') . '.bak';
        $fs     = new Filesystem();
        $exists = $fs->exists($file);
        if ($exists) {
            $fs->rename($file, $bak);
        }
        // アニメーションファイルが存在しないか
        $this->assertFalse($fs->exists($file));

        // アニメーション前の状態のみ有効にしてファイルを作成
        $Config = $this->getConfig();
        $this->gsapService->generate($Config);

        // 商品一覧にアクセス
        $crawler = $this->client->request('GET', $this->generateUrl('product_list'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // ファイルの内容を取得し、assetを実際のパスへ置換
        $gsap_js  = 'JsysListItemAnimator/assets/gsap/gsap.min.js';
        $search   = "{{ asset('{$gsap_js}', 'plugin') }}";
        $replace  = '/html/plugin/' . $gsap_js;
        $contents = str_replace($search, $replace, file_get_contents($file));

        // ファイルの内容がHTMLに含まれているか
        $this->assertStringContainsString($contents, $crawler->html());

        // ファイルを削除
        $fs->remove($file);
        // 退避したファイルがあれば元の名前へ戻す
        if ($exists) {
            $fs->rename($bak, $file);
        }
    }

    /**
     * プラグイン設定エンティティを取得します。
     *
     * @return Config
     */
    private function getConfig()
    {
        $faker  = $this->getFaker();
        $Config = $this->configRepo->get();
        $Config = !$Config ? new Config() : $Config;
        $Config
            ->setOptionHideBeforeAnimation(true)
            ->setOptionSlideAnimation(false)
            ->setSlideAnimationX(null)
            ->setSlideAnimationY(null)
            ->setDuration($faker->randomFloat(1, 0.1, 5.0))
            ->setOptionStagger(false)
            ->setStagger(null)
            ->setOptionScrollTrigger(false)
            ->setScrollTriggerStart(null)
            ->setOptionScrollTriggerMarkers(false)
            ->setOptionChangeScaleOnHover(false)
            ->setChangeScaleOnHoverRate(null)
            ->setChangeScaleOnHoverDuration(null);

        return $Config;
    }

}
