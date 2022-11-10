<?php

namespace Plugin\JsysListItemAnimator\Tests\Web\Admin;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use Plugin\JsysListItemAnimator\Repository\ConfigRepository;
use Plugin\JsysListItemAnimator\Service\GsapTwigService as Gsap;
use Symfony\Component\Filesystem\Filesystem;


class ConfigControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var ConfigRepository
     */
    protected $configRepo;

    /**
     * @var string
     */
    private $url;

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->faker      = $this->getFaker();
        $this->configRepo = static::getContainer()->get(ConfigRepository::class);
        $this->url        = $this->generateUrl('jsys_list_item_animator_admin_config');
    }

    /**
     * 設定画面へのルーティングが設定されているか
     *
     * @return void
     */
    public function testRouting()
    {
        $crawler = $this->client->request('GET', $this->url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString('一覧商品アニメーションプラグイン', $crawler->html());
    }

    /**
     * 全て無効の場合に問題なく登録できるか
     *
     * @return void
     */
    public function testSaveAllDisable()
    {
        // すでにアニメーションファイルが存在する場合はリネームして退避
        $file   = Gsap::getTwigDirPath($this->eccubeConfig) . Gsap::FNAME_TWIG;
        $bak    = $file . '.' . date('YmdHis') . '.bak';
        $fs     = new Filesystem();
        $exists = $fs->exists($file);
        if ($exists) {
            $fs->rename($file, $bak);
        }

        // データをPOST
        $this->client->request('POST', $this->url, [
            'config' => $this->createFormData(),
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->url));
        $crawler = $this->client->followRedirect();

        // 登録できたか
        $this->assertStringContainsString('登録しました。', $crawler->html());

        // ファイルを削除
        $fs->remove($file);
        // 退避したファイルがあれば元の名前へ戻す
        if ($exists) {
            $fs->rename($bak, $file);
        }

        // 想定通りの値で登録されているか
        $Config = $this->configRepo->get();
        $this->assertFalse($Config->isOptionHideBeforeAnimation());
        $this->assertFalse($Config->isOptionSlideAnimation());
        $this->assertNull($Config->getSlideAnimationX());
        $this->assertNull($Config->getSlideAnimationY());
        $this->assertNull($Config->getDuration());
        $this->assertFalse($Config->isOptionStagger());
        $this->assertNull($Config->getStagger());
        $this->assertFalse($Config->isOptionScrollTrigger());
        $this->assertNull($Config->getScrollTriggerStart());
        $this->assertFalse($Config->isOptionScrollTriggerMarkers());
        $this->assertFalse($Config->isOptionChangeScaleOnHover());
        $this->assertNull($Config->getChangeScaleOnHoverRate());
        $this->assertNull($Config->getChangeScaleOnHoverDuration());
    }

    /**
     * 全て有効の場合に問題なく登録できるか
     *
     * @return void
     */
    public function testSaveAllEnable()
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

        // データをPOST
        $form = $this->createFormData(false);
        $this->client->request('POST', $this->url, ['config' => $form]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->url));
        $crawler = $this->client->followRedirect();

        // 登録できたか
        $this->assertStringContainsString('登録しました。', $crawler->html());

        // ファイルを削除
        $fs->remove($file);
        // 退避したファイルがあれば元の名前へ戻す
        if ($exists) {
            $fs->rename($bak, $file);
        }

        // 想定通りの値で登録されているか
        $Config = $this->configRepo->get();
        $this->assertTrue($Config->isOptionHideBeforeAnimation());
        $this->assertTrue($Config->isOptionSlideAnimation());
        $this->assertSame($form['slide_animation_x'], $Config->getSlideAnimationX());
        $this->assertSame($form['slide_animation_y'], $Config->getSlideAnimationY());
        $this->assertSame(sprintf('%.2f', $form['duration']), $Config->getDuration());
        $this->assertTrue($Config->isOptionStagger());
        $this->assertSame(sprintf('%.2f', $form['stagger']), $Config->getStagger());
        $this->assertTrue($Config->isOptionScrollTrigger());
        $this->assertSame($form['scroll_trigger_start'], (int)$Config->getScrollTriggerStart());
        $this->assertTrue($Config->isOptionScrollTriggerMarkers());
        $this->assertTrue($Config->isOptionChangeScaleOnHover());
        $this->assertSame(
            $form['change_scale_on_hover_rate'],
            (int)$Config->getChangeScaleOnHoverRate()
        );
        $this->assertSame(
            sprintf('%.2f', $form['change_scale_on_hover_duration']),
            $Config->getChangeScaleOnHoverDuration()
        );
    }

    /**
     * 必須項目の検証失敗
     *
     * @return void
     */
    public function testValidationEmpty()
    {
        // データをPOST
        $form = array_merge($this->createFormData(), [
            'option_hide_before_animation'  => '1',
            'option_slide_animation'        => '1',
            'option_stagger'                => '1',
            'option_scroll_trigger'         => '1',
            'option_scroll_trigger_markers' => '1',
            'option_change_scale_on_hover'  => '1',
        ]);
        $crawler = $this->client->request('POST', $this->url, ['config' => $form]);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 検証失敗メッセージを確認
        $sele_err       = 'span.invalid-feedback .form-error-message';

        $err            = '入力されていません。';
        $id             = '#config_slide_animation_x';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_slide_animation_y';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_duration';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_stagger';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_scroll_trigger_start';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_change_scale_on_hover_rate';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_change_scale_on_hover_duration';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();
    }

    /**
     * 数値項目へ文字列を入力した場合の検証失敗
     *
     * @return void
     */
    public function testValidationNumeric()
    {
        // データをPOST
        $form = array_merge($this->createFormData(false), [
            'slide_animation_x'              => $this->faker->word,
            'slide_animation_y'              => $this->faker->word,
            'duration'                       => $this->faker->word,
            'stagger'                        => $this->faker->word,
            'scroll_trigger_start'           => $this->faker->word,
            'change_scale_on_hover_rate'     => $this->faker->word,
            'change_scale_on_hover_duration' => $this->faker->word,
        ]);
        $crawler = $this->client->request('POST', $this->url, ['config' => $form]);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 検証失敗メッセージを確認
        $sele_err       = 'span.invalid-feedback .form-error-message';

        $err            = '有効な値ではありません。';
        $id             = '#config_slide_animation_x';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_slide_animation_y';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_scroll_trigger_start';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_change_scale_on_hover_rate';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $err            = '数字で入力してください。';
        $id             = '#config_duration';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_stagger';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_change_scale_on_hover_duration';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();
    }

    /**
     * 数値項目に最小未満を入力した場合の検証失敗
     *
     * @return void
     */
    public function testValidationLessThanMin()
    {
        // データをPOST
        $form = array_merge($this->createFormData(false), [
            'duration'                       => 0,
            'stagger'                        => 0,
            'scroll_trigger_start'           => 0,
            'change_scale_on_hover_rate'     => 0,
            'change_scale_on_hover_duration' => 0,
        ]);
        $crawler = $this->client->request('POST', $this->url, ['config' => $form]);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 検証失敗メッセージを確認
        $sele_err       = 'span.invalid-feedback .form-error-message';

        $err            = '0より大きくなければなりません。';
        $id             = '#config_duration';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_stagger';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_change_scale_on_hover_duration';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $err            = '1以上100以下でなければなりません。';
        $id             = '#config_scroll_trigger_start';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $err            = '1以上500以下でなければなりません。';
        $id             = '#config_change_scale_on_hover_rate';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();
    }

    /**
     * 数値項目に最大より大きい値を入力した場合の検証失敗
     *
     * @return void
     */
    public function testValidationGreaterThanMax()
    {
        // データをPOST
        $form = array_merge($this->createFormData(false), [
            'duration'                       => 10,
            'stagger'                        => 10,
            'scroll_trigger_start'           => 101,
            'change_scale_on_hover_rate'     => 501,
            'change_scale_on_hover_duration' => 10,
        ]);
        $crawler = $this->client->request('POST', $this->url, ['config' => $form]);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 検証失敗メッセージを確認
        $sele_err       = 'span.invalid-feedback .form-error-message';

        $err            = '10未満でなければなりません。';
        $id             = '#config_duration';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_stagger';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $id             = '#config_change_scale_on_hover_duration';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $err            = '1以上100以下でなければなりません。';
        $id             = '#config_scroll_trigger_start';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();

        $err            = '1以上500以下でなければなりません。';
        $id             = '#config_change_scale_on_hover_rate';
        $this->expected = $err;
        $this->actual   = $crawler->filter($id . ' + ' . $sele_err)->text();
        $this->verify();
    }

    /**
     * 全て無効または全て有効にしたフォームデータを取得します。
     *
     * @param boolean $is_disable
     * @return array
     */
    private function createFormData($is_disable = true): array
    {
        if ($is_disable) {
            return [
                '_token'                         => 'dummy',
                'slide_animation_x'              => null,
                'slide_animation_y'              => null,
                'duration'                       => null,
                'stagger'                        => null,
                'scroll_trigger_start'           => null,
                'change_scale_on_hover_rate'     => null,
                'change_scale_on_hover_duration' => null,
            ];
        }

        return [
            '_token'                         => 'dummy',
            'option_hide_before_animation'   => '1',
            'option_slide_animation'         => '1',
            'slide_animation_x'              => $this->faker->numberBetween(-30, -10),
            'slide_animation_y'              => $this->faker->numberBetween(10, 30),
            'duration'                       => $this->faker->randomFloat(1, 0.1, 5.0),
            'option_stagger'                 => '1',
            'stagger'                        => $this->faker->randomFloat(1, 0.1, 5.0),
            'option_scroll_trigger'          => '1',
            'scroll_trigger_start'           => $this->faker->numberBetween(1, 90),
            'option_scroll_trigger_markers'  => '1',
            'option_change_scale_on_hover'   => '1',
            'change_scale_on_hover_rate'     => $this->faker->numberBetween(1, 300),
            'change_scale_on_hover_duration' => $this->faker->randomFloat(1, 0.1, 2.0),
        ];
    }

}
