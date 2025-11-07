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
    
    public function get_script_depends() {
        return ['anmi-video-banner-script'];
    }
    
    public function get_style_depends() {
        return ['anmi-video-banner-style'];
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
        $banners = AnMi_Banner_Video_Pro_Admin::fetch_all_active_banners();
        $banner_options = ['manual' => __('Manual Setup', 'anmi-video-banner')];
        
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                $banner_options[$banner->banner_id] = $banner->banner_name;
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
        
        // Video Settings Section (Manual Mode) - v1.6.13
        $this->start_controls_section(
            'section_video_settings',
            [
                'label' => __('🎛️ Video Settings', 'anmi-video-banner'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'banner_source' => 'manual',
                    'video_url!' => '',
                ],
            ]
        );
        
        $this->add_control(
            'video_autoplay',
            [
                'label' => __('▶️ Auto Play', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('On', 'anmi-video-banner'),
                'label_off' => __('Off', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '1',
                'description' => __('Automatically start video playback', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'video_muted',
            [
                'label' => __('🔇 Muted', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('On', 'anmi-video-banner'),
                'label_off' => __('Off', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '1',
                'description' => __('Start video with sound muted (recommended for autoplay)', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'video_loop',
            [
                'label' => __('🔁 Loop', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('On', 'anmi-video-banner'),
                'label_off' => __('Off', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '1',
                'description' => __('Loop video playback continuously', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'video_controls',
            [
                'label' => __('🎮 Show Controls', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'anmi-video-banner'),
                'label_off' => __('Hide', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '1',
                'description' => __('Display video player controls (play, pause, volume)', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'video_modestbranding',
            [
                'label' => __('📺 Hide YouTube Logo', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Hide', 'anmi-video-banner'),
                'label_off' => __('Show', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '1',
                'description' => __('Hide YouTube logo from control bar', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'video_rel',
            [
                'label' => __('🎬 Show Related Videos', 'anmi-video-banner'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'anmi-video-banner'),
                'label_off' => __('Hide', 'anmi-video-banner'),
                'return_value' => '1',
                'default' => '0',
                'description' => __('Show related videos when playback ends', 'anmi-video-banner'),
            ]
        );
        
        $this->add_control(
            'video_settings_note',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __('<div style="padding: 10px; background: #f0f0f1; border-left: 3px solid #0073aa; margin-top: 10px;">
                    <strong>ℹ️ Note:</strong> Some browsers may block autoplay with sound. It\'s recommended to keep "Muted" ON when using "Auto Play".
                    </div>', 'anmi-video-banner'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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
        $plugin = AnMi_Banner_Video_Pro::get_instance();

        if ($settings['banner_source'] !== 'manual') {
            $banner_id = isset($settings['banner_source']) ? intval($settings['banner_source']) : 0;

            if ($banner_id <= 0) {
                echo '<p style="color:red;">Banner not found. Please select a valid banner or use manual setup.</p>';
                return;
            }

            // Build attributes array with widget settings override
            $override_atts = array('id' => (string) $banner_id);
            
            // IMPORTANT: Only override if widget explicitly changed from default
            // Check if mobile_behavior is different from control default
            $mobile_behavior_control = $this->get_controls('mobile_behavior');
            $default_mobile = isset($mobile_behavior_control['default']) ? $mobile_behavior_control['default'] : 'image';
            
            if (isset($settings['mobile_behavior']) && $settings['mobile_behavior'] !== $default_mobile) {
                // Widget explicitly changed mobile_behavior → override database
                $override_atts['mobile_behavior'] = $settings['mobile_behavior'];
                $override_atts['_widget_override_mobile'] = '1'; // Flag for explicit override
            }
            
            // Override autoplay delay if changed from default (0)
            if (isset($settings['autoplay_delay']) && $settings['autoplay_delay'] != 0) {
                $override_atts['autoplay_delay'] = (string) $settings['autoplay_delay'];
                $override_atts['_widget_override_delay'] = '1';
            }

            echo $plugin->render_video_banner($override_atts);
            return;
        }

        $video_url = !empty($settings['video_url']) ? $settings['video_url'] : '';
        $images = array();

        if (!empty($settings['images']) && is_array($settings['images'])) {
            foreach ($settings['images'] as $image) {
                if (!empty($image['url'])) {
                    $images[] = $image['url'];
                }
            }
        }

        $height = '600px';
        $height_setting = $settings['height'] ?? null;
        if (is_array($height_setting)) {
            $size = $height_setting['size'] ?? null;
            $unit = $height_setting['unit'] ?? 'px';
            if ($size !== null && $size !== '') {
                $height = $size . $unit;
            }
        } elseif (!empty($height_setting)) {
            $height = $height_setting;
        }

        $manual_atts = array(
            'video_url' => $video_url,
            'images' => !empty($images) ? wp_json_encode($images) : '',
            'height' => $height,
            'title' => $settings['title'] ?? '',
            'subtitle' => $settings['subtitle'] ?? '',
            'button_text' => $settings['button_text'] ?? '',
            'button_link' => !empty($settings['button_link']['url']) ? $settings['button_link']['url'] : '#',
            'show_title' => !empty($settings['show_title']) ? '1' : '0',
            'show_subtitle' => !empty($settings['show_subtitle']) ? '1' : '0',
            'show_button' => !empty($settings['show_button']) ? '1' : '0',
            'transition' => $settings['transition'] ?? 'fade',
            'mobile_behavior' => $settings['mobile_behavior'] ?? 'image',
            'autoplay_delay' => isset($settings['autoplay_delay']) ? (string) $settings['autoplay_delay'] : '0',
            'slider_speed' => isset($settings['slider_speed']) ? (string) $settings['slider_speed'] : '3000',
            'slider_effect' => $settings['slider_effect'] ?? 'fade',
            'enable_autoplay' => !empty($settings['video_autoplay']) ? '1' : '0',
            'enable_muted' => !empty($settings['video_muted']) ? '1' : '0',
            'enable_loop' => !empty($settings['video_loop']) ? '1' : '0',
            'enable_controls' => !empty($settings['video_controls']) ? '1' : '0',
            'enable_modestbranding' => !empty($settings['video_modestbranding']) ? '1' : '0',
            'enable_rel' => !empty($settings['video_rel']) ? '1' : '0',
        );

        if (empty($manual_atts['video_url']) || empty($images)) {
            echo '<p style="color:red;">Please provide both video URL and at least one image.</p>';
            return;
        }

        echo $plugin->render_video_banner($manual_atts);
    }
}
