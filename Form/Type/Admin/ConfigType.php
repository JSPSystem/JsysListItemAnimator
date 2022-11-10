<?php

namespace Plugin\JsysListItemAnimator\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Form\Type\ToggleSwitchType;
use Plugin\JsysListItemAnimator\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConfigType extends AbstractType
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * JsysListItemAnimator ConfigType constructor.
     *
     * @param ValidatorInterface $validator
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        ValidatorInterface $validator,
        EccubeConfig $eccubeConfig
    ) {
        $this->validator    = $validator;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('option_hide_before_animation', ToggleSwitchType::class, [
                'label_on'  => 'jsys_listitem_animator.form.label.hide',
                'label_off' => 'jsys_listitem_animator.form.label.show',
            ])
            ->add('option_slide_animation', ToggleSwitchType::class)
            ->add('slide_animation_x', IntegerType::class, [
                'required' => false,
                'attr'     => [
                    'placeholder' => 'jsys_listitem_animator.form.placeholder.px',
                ],
            ])
            ->add('slide_animation_y', IntegerType::class, [
                'required' => false,
                'attr'     => [
                    'placeholder' => 'jsys_listitem_animator.form.placeholder.px',
                ],
            ])
            ->add('duration', TextType::class, [
                'required' => false,
                'attr'     => [
                    'placeholder' => 'jsys_listitem_animator.form.placeholder.seconds',
                ],
            ])
            ->add('option_stagger', ToggleSwitchType::class)
            ->add('stagger', TextType::class, [
                'required' => false,
                'attr'     => [
                    'placeholder' => 'jsys_listitem_animator.form.placeholder.seconds',
                ],
            ])
            ->add('option_scroll_trigger', ToggleSwitchType::class)
            ->add('scroll_trigger_start', NumberType::class, ['required' => false])
            ->add('option_scroll_trigger_markers', ToggleSwitchType::class, [
                'label_on'  => 'jsys_listitem_animator.form.label.show',
                'label_off' => 'jsys_listitem_animator.form.label.hide',
            ])
            ->add('option_change_scale_on_hover', ToggleSwitchType::class)
            ->add('change_scale_on_hover_rate', NumberType::class, ['required' => false])
            ->add('change_scale_on_hover_duration', TextType::class, [
                'required' => false,
                'attr'     => [
                    'placeholder' => 'jsys_listitem_animator.form.placeholder.seconds',
                ],
            ]);

        // 初期値の設定
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            $option_hide_before_animation = $data['option_hide_before_animation'] ?? false;
            $option_slide_animation       = $data['option_slide_animation'] ?? false;
            $option_scroll_trigger        = $data['option_scroll_trigger'] ?? false;

            // 非表示も移動もしない場合は、順番にアニメーションとスクロールトリガーを無効にする
            if (!$option_hide_before_animation && !$option_slide_animation) {
                $data['option_stagger']        = false;
                $data['option_scroll_trigger'] = false;
            }
            // スクロールトリガーが無効の場合は、開始位置の表示も無効にする
            if (!$option_scroll_trigger) {
                $data['option_scroll_trigger_markers'] = false;
            }

            $event->setData($data);
        });

        // 条件付きバリデーションの設定
        $this->addValidations($builder);
    }

    /**
     * 条件付きバリデーションを設定します。
     *
     * @param FormBuilderInterface $builder
     * @return void
     */
    protected function addValidations(FormBuilderInterface $builder)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Config $data */
            $data = $form->getData();
            $add  = function ($key, $value, $validations) use ($form) {
                $this->addErrors($key, $form, $this->validator->validate(
                    $value,
                    $validations
                ));
            };

            $option_slide_animation       = $form['option_slide_animation']->getData();
            $option_hide_before_animation = $form['option_hide_before_animation']->getData();
            $option_slide_animation       = $form['option_slide_animation']->getData();
            $option_stagger               = $form['option_stagger']->getData();
            $option_scroll_trigger        = $form['option_scroll_trigger']->getData();
            $option_change_scale_on_hover = $form['option_change_scale_on_hover']->getData();

            // 移動する場合に移動距離の検証を追加
            if ($option_slide_animation) {
                $add('slide_animation_x', $data->getSlideAnimationX(), [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_int_len']]),
                ]);
                $add('slide_animation_y', $data->getSlideAnimationY(), [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_int_len']]),
                ]);
            }
            // 非表示または移動する場合にアニメーションに使用する時間の検証を追加
            if ($option_hide_before_animation || $option_slide_animation) {
                $add('duration', $data->getDuration(), [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => "/^\d+(?:\.\d+)?$/u",
                        'message' => 'form_error.numeric_only',
                    ]),
                    new Assert\GreaterThan(0),
                    new Assert\LessThan(10),
                ]);
            }
            // 順番にアニメーションする場合に時間差の検証を追加
            if ($option_stagger) {
                $add('stagger', $data->getStagger(), [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => "/^\d+(?:\.\d+)?$/u",
                        'message' => 'form_error.numeric_only',
                    ]),
                    new Assert\GreaterThan(0),
                    new Assert\LessThan(10),
                ]);
            }
            // スクロールトリガーが有効な場合に開始位置の検証を追加
            if ($option_scroll_trigger) {
                $add('scroll_trigger_start', $data->getScrollTriggerStart(), [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form_error.numeric_only',
                    ]),
                    new Assert\Range(['min' => 1, 'max' => 100]),
                ]);
            }
            // マウスオーバーでサイズを変更する場合にサイズと時間の検証を追加
            if ($option_change_scale_on_hover) {
                $add('change_scale_on_hover_rate', $data->getChangeScaleOnHoverRate(), [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form_error.numeric_only',
                    ]),
                    new Assert\Range(['min' => 1, 'max' => 500,]),
                ]);
                $add('change_scale_on_hover_duration', $data->getChangeScaleOnHoverDuration(), [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => "/^\d+(?:\.\d+)?$/u",
                        'message' => 'form_error.numeric_only',
                    ]),
                    new Assert\GreaterThan(0),
                    new Assert\LessThan(10),
                ]);
            }
        });
    }

    /**
     * フォームにエラーを追加します。
     *
     * @param string $key
     * @param FormInterface $form
     * @param ConstraintViolationListInterface $errors
     * @return void
     */
    protected function addErrors(
        $key,
        FormInterface $form,
        ConstraintViolationListInterface $errors
    ) {
        foreach ($errors as $error) {
            $form[$key]->addError(new FormError($error->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
        ]);
    }
}
