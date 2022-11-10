<?php

namespace Plugin\JsysListItemAnimator\Tests\Service;

use Eccube\Tests\Service\AbstractServiceTestCase;
use Plugin\JsysListItemAnimator\Entity\Config;
use Plugin\JsysListItemAnimator\Repository\ConfigRepository;
use Plugin\JsysListItemAnimator\Service\GsapTwigService as Gsap;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;

class GsapTwigServiceTest extends AbstractServiceTestCase
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
     * すべて無効の場合、データ文字列が生成されないか
     *
     * @return void
     */
    public function testGenerateAllDisable()
    {
        $Config = $this->getConfig();
        $twig   = $this->gsapService->generate($Config, false);
        $this->assertEmpty($twig);
    }

    /**
     * すべて有効の場合のデータ文字列が想定されているものか
     *
     * @return void
     */
    public function testGenerateAllEnable()
    {
        // 生成されているか
        $Config = $this->getConfig('enable');
        $twig   = $this->gsapService->generate($Config, false);
        $this->assertNotEmpty($twig);

        $parts = $this->getStrEachParts($twig);
        $x     = $Config->getSlideAnimationX();
        $y     = $Config->getSlideAnimationY();
        $start = $Config->getScrollTriggerStart() . '%';
        $pos   = '"top' . ($y < 0 ? '-=' : '+=') . abs($y) . ' ' . $start . '"';
        $scale = $Config->getChangeScaleOnHoverRate() / 100;
        $hover = "scale: {$scale}, duration: {$Config->getChangeScaleOnHoverDuration()},";
        $leave = "scale: 1, duration: {$Config->getChangeScaleOnHoverDuration()},";

        // CSSが存在するか
        $this->assertNotEmpty($parts->css);
        // CSSの内容が想定通りか
        $this->assertStringContainsString('opacity: 0;', $parts->css);
        $this->assertStringContainsString('visibility: hidden;', $parts->css);
        $this->assertStringContainsString('position: relative;', $parts->css);
        $this->assertStringContainsString('left: ' . -$x . 'px;', $parts->css);
        $this->assertStringContainsString('top: ' . -$y . 'px;', $parts->css);

        // gsap・ScrollTriggerを読み込んでいるか
        $this->assertTrue($parts->gsap);
        $this->assertTrue($parts->scroll);

        // アニメーションjsが存在するか
        $this->assertNotEmpty($parts->js);
        // アニメーションjsの内容が想定通りか
        $this->assertStringContainsString('ScrollTrigger.batch(', $parts->js);
        $this->assertStringContainsString('onEnter: (batch) => { gsap.to(batch', $parts->js);
        $this->assertStringContainsString('autoAlpha: 1,', $parts->js);
        $this->assertStringContainsString("x: {$x},", $parts->js);
        $this->assertStringContainsString("y: {$y},", $parts->js);
        $this->assertStringContainsString("duration: {$Config->getDuration()},", $parts->js);
        $this->assertStringContainsString("stagger: {$Config->getStagger()}", $parts->js);
        $this->assertStringContainsString("start: {$pos},", $parts->js);
        $this->assertStringContainsString("markers: true", $parts->js);

        // マウスオーバーが存在するか
        $this->assertNotEmpty($parts->hover);
        // マウスオーバーの内容が想定通りか
        $this->assertStringContainsString($hover, $parts->hover);
        $this->assertStringContainsString($leave, $parts->hover);
    }

    /**
     * アニメーション前の状態のみ有効にした場合のデータ文字列が想定されているものか
     *
     * @return void
     */
    public function testGenerateHideEnable()
    {
        // 生成されているか
        $Config = $this->getConfig('hide');
        $twig   = $this->gsapService->generate($Config, false);
        $this->assertNotEmpty($twig);

        $parts = $this->getStrEachParts($twig);
        $x     = $Config->getSlideAnimationX();
        $y     = $Config->getSlideAnimationY();
        $start = $Config->getScrollTriggerStart() . '%';
        $pos   = '"top' . ($y < 0 ? '-=' : '+=') . abs($y) . ' ' . $start . '"';
        $scale = $Config->getChangeScaleOnHoverRate() / 100;
        $hover = "scale: {$scale}, duration: {$Config->getChangeScaleOnHoverDuration()},";
        $leave = "scale: 1, duration: {$Config->getChangeScaleOnHoverDuration()},";

        // CSSが存在するか
        $this->assertNotEmpty($parts->css);
        // CSSの内容が想定通りか
        $this->assertStringContainsString('opacity: 0;', $parts->css);
        $this->assertStringContainsString('visibility: hidden;', $parts->css);
        $this->assertStringNotContainsString('position: relative;', $parts->css);
        $this->assertStringNotContainsString('left: ' . -$x . 'px;', $parts->css);
        $this->assertStringNotContainsString('top: ' . -$y . 'px;', $parts->css);

        // gsapを読み込んでいるか・ScrollTriggerの読み込みはないか
        $this->assertTrue($parts->gsap);
        $this->assertFalse($parts->scroll);

        // アニメーションjsが存在するか
        $this->assertNotEmpty($parts->js);
        // アニメーションjsの内容が想定通りか
        $this->assertStringContainsString('gsap.to(".ec-shelfGrid__item"', $parts->js);
        $this->assertStringContainsString('autoAlpha: 1,', $parts->js);
        $this->assertStringContainsString("duration: {$Config->getDuration()}", $parts->js);
        $this->assertStringNotContainsString('ScrollTrigger.batch(', $parts->js);
        $this->assertStringNotContainsString('onEnter: (batch) => { gsap.to(batch', $parts->js);
        $this->assertStringNotContainsString("x: {$x},", $parts->js);
        $this->assertStringNotContainsString("y: {$y},", $parts->js);
        $this->assertStringNotContainsString("stagger: {$Config->getStagger()}", $parts->js);
        $this->assertStringNotContainsString("start: {$pos},", $parts->js);
        $this->assertStringNotContainsString("markers: true", $parts->js);

        // マウスオーバーが存在しないか
        $this->assertEmpty($parts->hover);
    }

    /**
     * アニメーション用ファイルが作成されているか
     *
     * @return void
     */
    public function testCreateFile()
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
        $Config = $this->getConfig('hide');
        $this->gsapService->generate($Config);

        // ファイルが存在するか
        $this->assertTrue($fs->exists($file));

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
     * @param string $mode
     * @return Config
     */
    private function getConfig($mode = 'disable')
    {
        $faker  = $this->getFaker();
        $Config = $this->getInitializedConfig();

        // 全て無効
        if ('disable' == $mode) {
            return $Config;
        }
        // アニメーション前の状態のみ有効（非表示）
        if ('hide' == $mode) {
            $Config
                ->setOptionHideBeforeAnimation(true)
                ->setDuration($faker->randomFloat(1, 0.1, 5.0));
            return $Config;
        }
        // すべて有効
        $Config
            ->setOptionHideBeforeAnimation(true)
            ->setOptionSlideAnimation(true)
            ->setSlideAnimationX($faker->numberBetween(-30, -10))
            ->setSlideAnimationY($faker->numberBetween(10, 30))
            ->setDuration($faker->randomFloat(1, 0.1, 5.0))
            ->setOptionStagger(true)
            ->setStagger($faker->randomFloat(1, 0.1, 5.0))
            ->setOptionScrollTrigger(true)
            ->setScrollTriggerStart($faker->numberBetween(1, 90))
            ->setOptionScrollTriggerMarkers(true)
            ->setOptionChangeScaleOnHover(true)
            ->setChangeScaleOnHoverRate($faker->numberBetween(1, 300))
            ->setChangeScaleOnHoverDuration($faker->randomFloat(1, 0.1, 2.0));
        return $Config;
    }

    /**
     * 初期化済みの設定エンティティを取得します。
     *
     * @return Config
     */
    private function getInitializedConfig()
    {
        $Config = $this->configRepo->get();
        $Config = !$Config ? new Config() : $Config;
        $Config
            ->setOptionHideBeforeAnimation(false)
            ->setOptionSlideAnimation(false)
            ->setSlideAnimationX(null)
            ->setSlideAnimationY(null)
            ->setDuration(null)
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

    /**
     * twig文字列から検証する部分ごとに抜き出したオブジェクトを取得します。
     * string css, boolean gsap, boolean scroll, string js, string hover
     *
     * @param string $twig
     * @return stdClass
     */
    private function getStrEachParts(string $twig)
    {
        $parts = new stdClass();
        $parts->css    = '';
        $parts->gsap   = false;
        $parts->scroll = false;
        $parts->js     = '';
        $parts->hover  = '';

        if (empty($twig)) {
            return $parts;
        }

        $matches = [];

        // CSS
        $ret = preg_match('/<style type="text\/css">.*<\/style>/s', $twig, $matches);
        if ($ret) {
            $parts->css = current($matches);
        }

        // gsap.min.js・ScrollTrigger.min.jsの有無
        $dir           = 'JsysListItemAnimator/assets/gsap';
        $gsap          = "{{ asset('{$dir}/gsap.min.js', 'plugin') }}";
        $scroll        = "{{ asset('{$dir}/ScrollTrigger.min.js', 'plugin') }}";
        $parts->gsap   = strpos($twig, $gsap) === false ? false : true;
        $parts->scroll = strpos($twig, $scroll) === false ? false : true;

        // js
        $ret = preg_match('/<script>.*<\/script>/s', $twig, $matches);
        if ($ret) {
            $parts->js = current($matches);
        }

        // js内にあるマウスオーバー
        $pattern = '/\$\("\.ec-shelfGrid__item"\)\.hover\(.*?; \}\);/s';
        $ret     = preg_match($pattern, $parts->js, $matches);
        if ($ret) {
            $parts->hover = current($matches);
        }

        return $parts;
    }

}
