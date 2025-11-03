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
        
        // Video & Image Section
        $this->start_controls_section(
            'section_media',
            [
                'label' => __('Video & Image', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'video_url',
            [
                'label' => __('Video URL', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'media_type' => 'video',
                'default' => [
                    'url' => '',
                ],
                'description' => __('Upload or select a video file (.mp4 recommended)', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'image_url',
            [
                'label' => __('Image Overlay', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'description' => __('This image will be shown initially, video plays on hover', 'anmi-video-banner'),
            ]
        );
        
        $this->end_controls_section();
        
        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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
        
        $video_url = !empty($settings['video_url']['url']) ? $settings['video_url']['url'] : '';
        $image_url = !empty($settings['image_url']['url']) ? $settings['image_url']['url'] : '';
        
        if (empty($video_url) || empty($image_url)) {
            echo '<p style="color:red;">Please select both video and image in widget settings.</p>';
            return;
        }
        
        $unique_id = 'anmi-vb-' . $this->get_id();
        $button_link = !empty($settings['button_link']['url']) ? $settings['button_link']['url'] : '#';
        ?>
        
        <div class="anmi-video-banner-container <?php echo esc_attr($unique_id); ?> transition-<?php echo esc_attr($settings['transition']); ?>" 
             data-autoplay-delay="<?php echo esc_attr($settings['autoplay_delay']); ?>"
             data-mobile-behavior="<?php echo esc_attr($settings['mobile_behavior']); ?>">
            
            <video class="anmi-banner-video" loop muted playsinline preload="auto">
                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
            </video>
            
            <div class="anmi-banner-image" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
            
            <?php if (!empty($settings['title']) || !empty($settings['subtitle']) || !empty($settings['button_text'])): ?>
            <div class="anmi-banner-content">
                <?php if (!empty($settings['title'])): ?>
                    <h1 class="anmi-banner-title"><?php echo esc_html($settings['title']); ?></h1>
                <?php endif; ?>
                
                <?php if (!empty($settings['subtitle'])): ?>
                    <p class="anmi-banner-subtitle"><?php echo esc_html($settings['subtitle']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($settings['button_text'])): ?>
                    <a href="<?php echo esc_url($button_link); ?>" class="anmi-banner-btn">
                        <?php echo esc_html($settings['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="anmi-banner-loader">
                <div class="spinner"></div>
            </div>
        </div>
        
        <?php
    }
}
