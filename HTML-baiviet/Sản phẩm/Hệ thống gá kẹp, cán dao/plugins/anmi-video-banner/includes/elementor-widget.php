<?php
/**
 * Elementor Widget for An Mi Video Banner
 */

if (!defined('ABSPATH')) {
    exit;
}

class AnMi_Video_Banner_Elementor_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'anmi_video_banner';
    }
    
    public function get_title() {
        return __('An Mi Video Banner', 'anmi-video-banner');
    }
    
    public function get_icon() {
        return 'eicon-video-camera';
    }
    
    public function get_categories() {
        return ['general'];
    }
    
    public function get_keywords() {
        return ['video', 'banner', 'hover', 'transition', 'anmi'];
    }
    
    protected function register_controls() {
        
        // ============================================
        // CONTENT TAB
        // ============================================
        
        // Banner Selection Section
        $this->start_controls_section(
            'section_banner_select',
            [
                'label' => __('Select Banner', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        // Get banners from database
        $banners = AnMi_Video_Banner_Admin::get_all_banners();
        $banner_options = ['manual' => __('Manual Setup', 'anmi-video-banner')];
        
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                $banner_options[$banner->id] = $banner->name;
            }
        }
        
        $this->add_control(
            'banner_source',
            [
                'label' => __('Banner Source', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $banner_options,
                'default' => 'manual',
                'description' => __('Select from saved banners or configure manually', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'banner_note',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __('<a href="' . admin_url('admin.php?page=anmi-video-banner-new') . '" target="_blank">Create New Banner</a> in Admin Panel', 'anmi-video-banner'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition' => [
                    'banner_source!' => 'manual',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Video & Image Section (Manual Mode)
        $this->start_controls_section(
            'section_media',
            [
                'label' => __('Video & Images', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'banner_source' => 'manual',
                ],
            ]
        );
        
        $this->add_control(
            'video_url',
            [
                'label' => __('Video URL', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => 'https://yourdomain.com/video.mp4',
                'description' => __('YouTube, Vimeo, or direct MP4 URL', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'images',
            [
                'label' => __('Slider Images', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::GALLERY,
                'default' => [],
                'description' => __('Upload multiple images for the slider', 'anmi-video-banner'),
            ]
        );
        
        $this->end_controls_section();
        
        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'banner_source' => 'manual',
                ],
            ]
        );
        
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Your Amazing Title', 'anmi-video-banner'),
                'placeholder' => __('Enter title', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'subtitle',
            [
                'label' => __('Subtitle', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Your subtitle or description text here', 'anmi-video-banner'),
                'placeholder' => __('Enter subtitle', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Learn More', 'anmi-video-banner'),
                'placeholder' => __('Enter button text', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'button_link',
            [
                'label' => __('Button Link', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'anmi-video-banner'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );
        
        // Visibility Controls Separator
        $this->add_control(
            'visibility_divider',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        
        $this->add_control(
            'visibility_heading',
            [
                'label' => __('Content Visibility', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );
        
        $this->add_control(
            'show_title',
            [
                'label' => __('Show Title', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'anmi-video-banner'),
                'label_off' => __('Hide', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '0',
                'description' => __('Toggle to show/hide the title on banner. Default: Hidden', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'show_subtitle',
            [
                'label' => __('Show Subtitle', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'anmi-video-banner'),
                'label_off' => __('Hide', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '0',
                'description' => __('Toggle to show/hide the subtitle on banner. Default: Hidden', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'show_button',
            [
                'label' => __('Show CTA Button', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'anmi-video-banner'),
                'label_off' => __('Hide', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '0',
                'description' => __('Toggle to show/hide the call-to-action button. Default: Hidden', 'anmi-video-banner'),
            ]
        );
        
        $this->end_controls_section();
        
        // Settings Section
        $this->start_controls_section(
            'section_settings',
            [
                'label' => __('Settings', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_responsive_control(
            'height',
            [
                'label' => __('Height', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 600,
                ],
                'selectors' => [
                    '{{WRAPPER}} .anmi-video-banner-container' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'transition',
            [
                'label' => __('Transition Effect', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'fade',
                'options' => [
                    'fade' => __('Fade', 'anmi-video-banner'),
                    'zoom' => __('Zoom', 'anmi-video-banner'),
                    'blur' => __('Blur', 'anmi-video-banner'),
                    'slide' => __('Slide', 'anmi-video-banner'),
                ],
            ]
        );
        
        $this->add_control(
            'autoplay_delay',
            [
                'label' => __('Autoplay Delay (seconds)', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'max' => 10,
                'step' => 0.5,
                'description' => __('Delay before video plays on hover (0 = instant)', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'mobile_behavior',
            [
                'label' => __('Mobile Behavior', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'image',
                'options' => [
                    'image' => __('Show Image Only', 'anmi-video-banner'),
                    'video' => __('Show Video Only', 'anmi-video-banner'),
                    'both' => __('Allow Video on Touch', 'anmi-video-banner'),
                ],
                'description' => __('How to display on mobile devices', 'anmi-video-banner'),
            ]
        );
        
        $this->end_controls_section();
        
        // ============================================
        // STYLE TAB
        // ============================================
        
        // Title Style
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __('Title', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .anmi-banner-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .anmi-banner-title',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'selector' => '{{WRAPPER}} .anmi-banner-title',
            ]
        );
        
        $this->end_controls_section();
        
        // Button Style
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => __('Button', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'button_color',
            [
                'label' => __('Text Color', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .anmi-banner-btn' => 'color: {{VALUE}} !important;',
                ],
            ]
        );
        
        $this->add_control(
            'button_bg_color',
            [
                'label' => __('Background Color', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff6600',
                'selectors' => [
                    '{{WRAPPER}} .anmi-banner-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => __('Hover Background Color', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff8533',
                'selectors' => [
                    '{{WRAPPER}} .anmi-banner-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .anmi-banner-btn',
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Check if using database banner or manual setup
        if ($settings['banner_source'] !== 'manual') {
            // Load from database
            $banner_id = intval($settings['banner_source']);
            $banner = AnMi_Video_Banner_Admin::get_banner($banner_id);
            
            if (!$banner) {
                echo '<p style="color:red;">Banner not found. Please select a valid banner or use manual setup.</p>';
                return;
            }
            
            // Use database values
            $video_url = $banner->video_url;
            $images = json_decode($banner->images, true);
            $title = $banner->title;
            $subtitle = $banner->subtitle;
            $button_text = $banner->button_text;
            $button_link = $banner->button_link;
            $show_title = isset($banner->show_title) ? $banner->show_title : 0;
            $show_subtitle = isset($banner->show_subtitle) ? $banner->show_subtitle : 0;
            $show_button = isset($banner->show_button) ? $banner->show_button : 0;
            $height = $banner->height;
            $transition = $banner->transition;
            $slider_speed = $banner->slider_speed;
            $autoplay_delay = $banner->autoplay_delay;
            $mobile_behavior = $banner->mobile_behavior;
        } else {
            // Use manual setup
            $video_url = !empty($settings['video_url']) ? $settings['video_url'] : '';
            $images = array();
            
            if (!empty($settings['images'])) {
                foreach ($settings['images'] as $image) {
                    $images[] = $image['url'];
                }
            }
            
            $title = $settings['title'];
            $subtitle = $settings['subtitle'];
            $button_text = $settings['button_text'];
            $button_link = !empty($settings['button_link']['url']) ? $settings['button_link']['url'] : '#';
            $show_title = !empty($settings['show_title']) ? 1 : 0;
            $show_subtitle = !empty($settings['show_subtitle']) ? 1 : 0;
            $show_button = !empty($settings['show_button']) ? 1 : 0;
            $height = $settings['height'];
            $transition = $settings['transition'];
            $slider_speed = $settings['slider_speed'];
            $autoplay_delay = $settings['autoplay_delay'];
            $mobile_behavior = $settings['mobile_behavior'];
        }
        
        // Validate
        if (empty($video_url) || empty($images)) {
            echo '<p style="color:red;">Please provide both video URL and at least one image.</p>';
            return;
        }
        
        $unique_id = 'anmi-vb-' . $this->get_id();
        ?>
        
        <div class="anmi-video-banner-container <?php echo esc_attr($unique_id); ?> transition-<?php echo esc_attr($transition); ?>" 
             style="height: <?php echo esc_attr($height); ?>;"
             data-autoplay-delay="<?php echo esc_attr($autoplay_delay); ?>"
             data-mobile-behavior="<?php echo esc_attr($mobile_behavior); ?>"
             data-slider-speed="<?php echo esc_attr($slider_speed); ?>"
             data-slider-effect="fade">
            
            <!-- Video Background -->
            <video class="anmi-banner-video" loop muted playsinline preload="auto">
                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
            </video>
            
            <!-- Image Slider -->
            <div class="anmi-banner-slider">
                <?php foreach ($images as $index => $image_url): ?>
                    <div class="anmi-slider-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                         style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
                <?php endforeach; ?>
                
                <?php if (count($images) > 1): ?>
                    <div class="anmi-slider-dots">
                        <?php foreach ($images as $index => $image_url): ?>
                            <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($title) || !empty($subtitle) || !empty($button_text)): ?>
            <!-- Content Overlay -->
            <div class="anmi-banner-content"
                 data-show-title="<?php echo esc_attr($show_title); ?>"
                 data-show-subtitle="<?php echo esc_attr($show_subtitle); ?>"
                 data-show-button="<?php echo esc_attr($show_button); ?>">
                <?php if (!empty($title) && $show_title): ?>
                    <h1 class="anmi-banner-title"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>
                
                <?php if (!empty($subtitle) && $show_subtitle): ?>
                    <p class="anmi-banner-subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($button_text) && $show_button): ?>
                    <a href="<?php echo esc_url($button_link); ?>" class="anmi-banner-btn">
                        <?php echo esc_html($button_text); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Loading Spinner -->
            <div class="anmi-banner-loader">
                <div class="spinner"></div>
            </div>
        </div>
        
        <?php
    }
}
