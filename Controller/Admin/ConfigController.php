<?php

namespace Plugin\JsysListItemAnimator\Controller\Admin;

use Eccube\Controller\AbstractController;
use Exception;
use Plugin\JsysListItemAnimator\Form\Type\Admin\ConfigType;
use Plugin\JsysListItemAnimator\Repository\ConfigRepository;
use Plugin\JsysListItemAnimator\Service\GsapTwigService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var GsapTwigService
     */
    protected $gsapTwigService;

    /**
     * ConfigController constructor.
     *
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        ConfigRepository $configRepository,
        GsapTwigService $gsapTwigService
    ) {
        $this->configRepository = $configRepository;
        $this->gsapTwigService  = $gsapTwigService;
    }

    /**
     * @Route("/%eccube_admin_route%/jsys_list_item_animator/config", name="jsys_list_item_animator_admin_config")
     * @Template("@JsysListItemAnimator/admin/config.twig")
     */
    public function index(Request $request)
    {
        $Config = $this->configRepository->get();
        $form   = $this->createForm(ConfigType::class, $Config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Config = $form->getData();
            $this->entityManager->persist($Config);
            $this->entityManager->flush();

            // アニメーション用Twigを出力
            try {
                $this->gsapTwigService->generate($Config);

            } catch (Exception $ex) {
                log_info('JsysListItemAnimator Twig出力失敗', ['Error' => $ex->getMessage()]);
                $this->addError('アニメーションファイルの出力に失敗しました。', 'admin');
                return $this->redirectToRoute('jsys_list_item_animator_admin_config');
            }

            $this->addSuccess('登録しました。', 'admin');
            return $this->redirectToRoute('jsys_list_item_animator_admin_config');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
