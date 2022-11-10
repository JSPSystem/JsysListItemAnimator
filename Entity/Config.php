<?php

namespace Plugin\JsysListItemAnimator\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Plugin\JsysListItemAnimator\Entity\Config', false)) {
    /**
     * JsysListItemAnimator Config
     *
     * @ORM\Table(name="plg_jsys_list_item_animator_config")
     * @ORM\Entity(repositoryClass="Plugin\JsysListItemAnimator\Repository\ConfigRepository")
     */
    class Config
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var boolean
         * 
         * @ORM\Column(name="option_hide_before_animation", type="boolean", options={
         *   "default":false
         * })
         */
        private $option_hide_before_animation;

        /**
         * @var boolean
         * 
         * @ORM\Column(name="option_slide_animation", type="boolean", options={
         *   "default":false
         * })
         */
        private $option_slide_animation;

        /**
         * @var int|null
         * 
         * @ORM\Column(name="slide_animation_x", type="integer", nullable=true)
         */
        private $slide_animation_x;

        /**
         * @var int|null
         * 
         * @ORM\Column(name="slide_animation_y", type="integer", nullable=true)
         */
        private $slide_animation_y;

        /**
         * @var string|null
         * 
         * @ORM\Column(
         *   name="duration",
         *   type="decimal",
         *   precision=12,
         *   scale=2,
         *   options={"unsigned":true},
         *   nullable=true
         * )
         */
        private $duration;

        /**
         * @var boolean
         * 
         * @ORM\Column(name="option_stagger", type="boolean", options={"default":false})
         */
        private $option_stagger;

        /**
         * @var string|null
         * 
         * @ORM\Column(
         *   name="stagger",
         *   type="decimal",
         *   precision=12,
         *   scale=2,
         *   options={"unsigned":true},
         *   nullable=true
         * )
         */
        private $stagger;

        /**
         * @var boolean
         * 
         * @ORM\Column(name="option_scroll_trigger", type="boolean", options={"default":false})
         */
        private $option_scroll_trigger;

        /**
         * @var string|null
         * 
         * @ORM\Column(
         *   name="scroll_trigger_start",
         *   type="decimal",
         *   precision=10,
         *   scale=0,
         *   options={"unsigned":true},
         *   nullable=true
         * )
         */
        private $scroll_trigger_start;

        /**
         * @var boolean
         * 
         * @ORM\Column(name="option_scroll_trigger_markers", type="boolean", options={
         *   "default":false
         * })
         */
        private $option_scroll_trigger_markers;

        /**
         * @var boolean
         * 
         * @ORM\Column(name="option_change_scale_on_hover", type="boolean", options={
         *   "default":false
         * })
         */
        private $option_change_scale_on_hover;

        /**
         * @var string|null
         * 
         * @ORM\Column(
         *   name="change_scale_on_hover_rate",
         *   type="decimal",
         *   precision=10,
         *   scale=0,
         *   options={"unsigned":true},
         *   nullable=true
         * )
         */
        private $change_scale_on_hover_rate;

        /**
         * @var string|null
         * 
         * @ORM\Column(
         *   name="change_scale_on_hover_duration",
         *   type="decimal",
         *   precision=12,
         *   scale=2,
         *   options={"unsigned":true},
         *   nullable=true
         * )
         */
        private $change_scale_on_hover_duration;

        /**
         * @var \DateTime
         * 
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         * 
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;


        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * アニメーション前の商品を非表示にするか設定します。
         *
         * @param boolean $option_hide_before_animation
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setOptionHideBeforeAnimation($option_hide_before_animation)
        {
            $this->option_hide_before_animation = $option_hide_before_animation;
            return $this;
        }

        /**
         * アニメーション前の商品が非表示かどうか取得します。
         *
         * @return boolean
         */
        public function isOptionHideBeforeAnimation()
        {
            return $this->option_hide_before_animation;
        }

        /**
         * スライドアニメーションを有効にするか設定します。
         *
         * @param boolean $option_slide_animation
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setOptionSlideAnimation($option_slide_animation)
        {
            $this->option_slide_animation = $option_slide_animation;
            return $this;
        }

        /**
         * スライドアニメーションが有効かどうか取得します。
         *
         * @return boolean
         */
        public function isOptionSlideAnimation()
        {
            return $this->option_slide_animation;
        }

        /**
         * 横にスライドさせる距離（px）を設定します。
         * 負の値は左へ、正の値は右へスライドしながら元の位置に戻ります。
         *
         * @param int|null $slide_animation_x
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setSlideAnimationX($slide_animation_x)
        {
            $this->slide_animation_x = $slide_animation_x;
            return $this;
        }

        /**
         * 横にスライドさせる距離（px）を取得します。
         *
         * @return int|null
         */
        public function getSlideAnimationX()
        {
            return $this->slide_animation_x;
        }

        /**
         * 縦にスライドさせる距離（px）を設定します。
         * 負の値は上へ、正の値は下へスライドしながら元の位置に戻ります。
         *
         * @param int|null $slide_animation_y
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setSlideAnimationY($slide_animation_y)
        {
            $this->slide_animation_y = $slide_animation_y;
            return $this;
        }

        /**
         * 縦にスライドさせる距離（px）を取得します。
         *
         * @return int|null
         */
        public function getSlideAnimationY()
        {
            return $this->slide_animation_y;
        }

        /**
         * アニメーションに使用する時間（秒）を設定します。
         *
         * @param string|null $duration
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setDuration($duration)
        {
            $this->duration = $duration;
            return $this;
        }

        /**
         * アニメーションに使用する時間（秒）を取得します。
         *
         * @return string|null
         */
        public function getDuration()
        {
            return $this->duration;
        }

        /**
         * アニメーションを左上から順番にずらして開始するか設定します。
         *
         * @param boolean $option_stagger
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setOptionStagger($option_stagger)
        {
            $this->option_stagger = $option_stagger;
            return $this;
        }

        /**
         * アニメーションを左上から順番にずらして開始するかどうか取得します。
         *
         * @return boolean
         */
        public function isOptionStagger()
        {
            return $this->option_stagger;
        }

        /**
         * アニメーションをずらす間隔（秒）を設定します。
         *
         * @param string|null $stagger
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setStagger($stagger)
        {
            $this->stagger = $stagger;
            return $this;
        }

        /**
         * アニメーションをずらす間隔（秒）を取得します。
         *
         * @return string|null
         */
        public function getStagger()
        {
            return $this->stagger;
        }

        /**
         * スクロールによってアニメーションを開始するか設定します。
         *
         * @param boolean $option_scroll_trigger
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setOptionScrollTrigger($option_scroll_trigger)
        {
            $this->option_scroll_trigger = $option_scroll_trigger;
            return $this;
        }

        /**
         * スクロールによってアニメーションを開始するかどうか取得します。
         *
         * @return boolean
         */
        public function isOptionScrollTrigger()
        {
            return $this->option_scroll_trigger;
        }

        /**
         * アニメーションを開始する位置（%）を設定します。
         * 例えば、50の場合は商品が画面中央より上になるとアニメーションを開始します。
         *
         * @param string|null $scroll_trigger_start
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setScrollTriggerStart($scroll_trigger_start)
        {
            $this->scroll_trigger_start = $scroll_trigger_start;
            return $this;
        }

        /**
         * アニメーションを開始する位置（%）を取得します。
         *
         * @return string|null
         */
        public function getScrollTriggerStart()
        {
            return $this->scroll_trigger_start;
        }

        /**
         * アニメーションの開始位置をフロントに表示するか設定します。
         *
         * @param boolean $option_scroll_trigger_markers
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setOptionScrollTriggerMarkers($option_scroll_trigger_markers)
        {
            $this->option_scroll_trigger_markers = $option_scroll_trigger_markers;
            return $this;
        }

        /**
         * アニメーションの開始位置をフロントに表示するかどうか取得します。
         *
         * @return boolean
         */
        public function isOptionScrollTriggerMarkers()
        {
            return $this->option_scroll_trigger_markers;
        }

        /**
         * マウスオーバーで商品エリアのサイズを変更するか設定します。
         *
         * @param boolean $option_change_scale_on_hover
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setOptionChangeScaleOnHover($option_change_scale_on_hover)
        {
            $this->option_change_scale_on_hover = $option_change_scale_on_hover;
            return $this;
        }

        /**
         * マウスオーバーで商品エリアのサイズを変更するかどうか取得します。
         *
         * @return boolean
         */
        public function isOptionChangeScaleOnHover()
        {
            return $this->option_change_scale_on_hover;
        }

        /**
         * マウスオーバーで変更するサイズ（%）を設定します。
         *
         * @param string|null $change_scale_on_hover_rate
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setChangeScaleOnHoverRate($change_scale_on_hover_rate)
        {
            $this->change_scale_on_hover_rate = $change_scale_on_hover_rate;
            return $this;
        }

        /**
         * マウスオーバーで変更するサイズ（%）を取得します。
         *
         * @return string|null
         */
        public function getChangeScaleOnHoverRate()
        {
            return $this->change_scale_on_hover_rate;
        }

        /**
         * マウスオーバーのサイズ変更に使用する時間（秒）を設定します。
         * 
         * @param string|null $change_scale_on_hover_duration
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setChangeScaleOnHoverDuration($change_scale_on_hover_duration)
        {
            $this->change_scale_on_hover_duration = $change_scale_on_hover_duration;
            return $this;
        }

        /**
         * マウスオーバーのサイズ変更に使用する時間（秒）を取得します。
         *
         * @return void
         */
        public function getChangeScaleOnHoverDuration()
        {
            return $this->change_scale_on_hover_duration;
        }

        /**
         * 登録日を設定します。
         *
         * @param \DateTime $createDate
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;
            return $this;
        }

        /**
         * 登録日を取得します。
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * 更新日を設定します。
         *
         * @param \DateTime $updateDate
         * @return \Plugin\JsysListItemAnimator\Entity\Config
         */
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;
            return $this;
        }

        /**
         * 更新日を取得します。
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

    }

}
