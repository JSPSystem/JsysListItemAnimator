<?php

namespace Plugin\JsysListItemAnimator\Controller\Admin;

use Eccube\Controller\AbstractController;
use Plugin\JsysListItemAnimator\Form\Type\Admin\ConfigType;
use Plugin\JsysListItemAnimator\Repository\ConfigRepository;
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
     * ConfigController constructor.
     *
     * @param ConfigRepository $configRepository
     */
    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/jsys_list_item_animator/config", name="jsys_list_item_animator_admin_config")
     * @Template("@JsysListItemAnimator/admin/config.twig")
     */
    public function index(Request $request)
    {
        $Config = $this->configRepository->get();
        $form = $this->createForm(ConfigType::class, $Config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Config = $form->getData();
            $this->entityManager->persist($Config);
            $this->entityManager->flush();
            $this->addSuccess('登録しました。', 'admin');

            return $this->redirectToRoute('jsys_list_item_animator_admin_config');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
