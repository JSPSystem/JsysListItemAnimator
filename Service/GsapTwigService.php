<?php

namespace Plugin\JsysListItemAnimator\Service;

use Eccube\Common\EccubeConfig;
use Plugin\JsysListItemAnimator\Entity\Config;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 商品一覧用にGSAPのjsを追加したTwigを作成するサービスです。
 */
class GsapTwigService
{
    /**
     * アニメーション用Twigファイル名
     * @var string
     */
    public const FNAME_TWIG = 'animation.twig';

    /**
     * @var string
     */
    private const PLUGIN_CODE = 'JsysListItemAnimator';

    /**
     * @var string
     */
    private const SELECTOR_LISTITEM = '.ec-shelfGrid__item';

    /**
     * @var string
     */
    private const FNAME_GSAP = 'gsap.min.js';

    /**
     * @var string
     */
    private const FNAME_SCROLL = 'ScrollTrigger.min.js';

    /**
     * @var string
     */
    private const GSAP_EASE = 'power4.out';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $path_twig;

    /**
     * @var string
     */
    private $path_gsap;

    /**
     * @var string
     */
    private $path_scroll;

    /**
     * アニメーション用Twigファイルのディレクトリパスを取得します。
     *
     * @param EccubeConfig $eccubeConfig
     * @return string
     */
    public static function getTwigDirPath(EccubeConfig $eccubeConfig)
    {
        return $eccubeConfig['plugin_data_realdir'] . '/' . self::PLUGIN_CODE . '/';
    }

    /**
     * GsapTwigService constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $dir_gsap          = self::PLUGIN_CODE . '/assets/gsap/';
        $this->path_twig   = self::getTwigDirPath($eccubeConfig) . self::FNAME_TWIG;
        $this->path_gsap   = $dir_gsap . self::FNAME_GSAP;
        $this->path_scroll = $dir_gsap . self::FNAME_SCROLL;
    }

    /**
     * アニメーション用文字列を生成し、ファイルを作成します。
     *
     * @param Config $Config
     * @param boolean $is_create
     * @return string
     */
    public function generate(Config $Config, $is_create = true)
    {
        $this->config = $Config;

        $twig = $this->getCssString() . $this->getJsString();
        if ($is_create) {
            $fs = new Filesystem();
            $fs->dumpFile($this->path_twig, $twig);
        }
        return $twig;
    }

    /**
     * CSS文字列を取得します。
     *
     * @return string
     */
    protected function getCssString()
    {
        $css = [];

        // アニメーション前の状態が非表示ならopacity、visibilityを追加
        if ($this->config->isOptionHideBeforeAnimation()) {
            $css[] = 'opacity: 0;';
            $css[] = 'visibility: hidden;';
        }
        // 移動させる場合はpositionとtop、leftを追加、値は+-反転
        if ($this->config->isOptionSlideAnimation()) {
            $css[] = 'position: relative;';

            $x = -$this->config->getSlideAnimationX();
            $y = -$this->config->getSlideAnimationY();
            if ($x) {
                $css[] = "left: {$x}px;";
            }
            if ($y) {
                $css[] = "top: {$y}px;";
            }
        }

        if (!$css) {
            return '';
        }
        return '<style type="text/css"> '
             . self::SELECTOR_LISTITEM . ' { ' . implode(' ', $css) . ' } '
             . '</style>';
    }

    /**
     * JavaScript文字列を取得します。
     *
     * @return string
     */
    protected function getJsString()
    {
        if (
            !$this->config->isOptionHideBeforeAnimation() &&
            !$this->config->isOptionSlideAnimation() &&
            !$this->config->isOptionChangeScaleOnHover()
        ) {
            // アニメーションしない
            return '';
        }

        $js = [];

        // 使用するjsファイルの読み込みを追加
        $js[] = "<script src=\"{{ asset('{$this->path_gsap}', 'plugin') }}\"></script>";
        if ($this->config->isOptionScrollTrigger()) {
            $js[] = "<script src=\"{{ asset('{$this->path_scroll}', 'plugin') }}\"></script>";
        }

        // 基本アニメーションを追加
        $js[] = '<script>';
        $js[] = '$(function() {';
        $gsap = $this->getAnimationGsapString();
        if (
            $this->config->isOptionScrollTrigger() &&
            !empty($this->config->getScrollTriggerStart())
        ) {
            $js[] = $this->getScrollTriggerString($gsap);
        } else {
            $js[] = 'gsap.to("' . self::SELECTOR_LISTITEM . '", {' . $gsap . '});';
        }

        // マウスオーバー時のアニメーションを追加
        if ($this->config->isOptionChangeScaleOnHover()) {
            $js[] = $this->getHoverString();
        }
        $js[] = '});';
        $js[] = '</script>';

        return implode(' ', $js);
    }

    /**
     * 基本アニメーションのjs文字列を取得します。
     *
     * @return string
     */
    protected function getAnimationGsapString()
    {
        $js = [
            'ease: "' . self::GSAP_EASE . '"',
            'overwrite: true',
        ];

        // アニメーション前の状態が非表示ならautoAlpha追加
        if ($this->config->isOptionHideBeforeAnimation()) {
            $js[] = 'autoAlpha: 1';
        }
        // 移動させる場合はx、yを追加
        if ($this->config->isOptionSlideAnimation()) {
            if ($this->config->getSlideAnimationX()) {
                $js[] = 'x: ' . $this->config->getSlideAnimationX();
            }
            if ($this->config->getSlideAnimationY()) {
                $js[] = 'y: ' . $this->config->getSlideAnimationY();
            }
        }
        // 非表示または移動させる場合はdurationを追加
        if (
            $this->config->isOptionHideBeforeAnimation() ||
            $this->config->isOptionSlideAnimation()
        ) {
            if ($this->config->getDuration()) {
                $js[] = 'duration: ' . $this->config->getDuration();
            }
        }
        // 順番にアニメーションさせる場合はstaggerを追加
        if ($this->config->isOptionStagger()) {
            if ($this->config->getStagger()) {
                $js[] = 'stagger: ' . $this->config->getStagger();
            }
        }

        return implode(',', $js);
    }

    /**
     * スクロールトリガー有効時のjs文字列を取得します。
     *
     * @param string $gsap
     * @return void
     */
    protected function getScrollTriggerString($gsap)
    {
        $js = [
            'onEnter: (batch) => { gsap.to(batch, {' . $gsap . '}); }',
            'once: true',
        ];

        // 開始位置を追加、縦に移動させる場合は位置を調整
        $y = $this->config->getSlideAnimationY() ?? '';
        if ($this->config->isOptionSlideAnimation() && $y) {
            $y = ($y < 0 ? '-=' : '+=') . abs($y);
        }
        $js[] = 'start: "top' . $y . ' ' . $this->config->getScrollTriggerStart() . '%"';

        // 開始位置を表示する場合はmarkersを追加
        if ($this->config->isOptionScrollTriggerMarkers()) {
            $js[] = 'markers: true';
        }

        $js = implode(',', $js);
        return 'ScrollTrigger.batch("' . self::SELECTOR_LISTITEM . '", {' . $js . '});';
    }

    /**
     * マウスオーバー有効時のjs文字列を取得します。
     *
     * @return string
     */
    protected function getHoverString()
    {
        if (
            !$this->config->getChangeScaleOnHoverRate() ||
            !$this->config->getChangeScaleOnHoverDuration()
        ) {
            return '';
        }

        $scale    = $this->config->getChangeScaleOnHoverRate() / 100;
        $duration = $this->config->getChangeScaleOnHoverDuration();

        $hover = "scale: {$scale}, "
               . "duration: {$duration}, "
               . 'ease: "' . self::GSAP_EASE . '"';
        $leave = 'scale: 1, '
               . "duration: {$duration}, "
               . 'ease: "' . self::GSAP_EASE . '"';

        return '$("' . self::SELECTOR_LISTITEM . '").hover( '
             . 'function() { gsap.to($(this), { ' . $hover . ' }); }, '
             . 'function() { gsap.to($(this), { ' . $leave . ' }); }'
             . ');';
    }

}
