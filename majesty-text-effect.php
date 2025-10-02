<?php
/**
 * Plugin Name: Majesty Text Effect
 * Plugin URI: https://github.com/frankymbieleu/Majesty-Text-Effect
 * Description: Effet machine à écrire avec curseur clignotant et multiples styles
 * Version: 1.0.0
 * Author: FRANKY MBIELEU
 * Author URI: https://github.com/frankymbieleu
 * License: #
 * Text Domain: majesty-text-effect
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// Définir les constantes du plugin
define('MTE_VERSION', '1.0.0');
define('MTE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MTE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MTE_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Classe principale du plugin Majesty Text Effect
 * Préfixe: mte_
 */
if (!class_exists('Majesty_Text_Effect_Plugin')) {
    
    class Majesty_Text_Effect_Plugin {
        
        /**
         * Instance unique de la classe (Singleton)
         * @var Majesty_Text_Effect_Plugin
         */
        private static $mte_instance = null;
        
        /**
         * Nonce action pour la sécurité
         * @var string
         */
        private $mte_nonce_action = 'mte_settings_nonce_action';
        
        /**
         * Nonce name pour la sécurité
         * @var string
         */
        private $mte_nonce_name = 'mte_settings_nonce';
        
        /**
         * Capacité requise pour gérer les paramètres
         * @var string
         */
        private $mte_required_capability = 'manage_options';
        
        /**
         * Constructeur privé pour le pattern Singleton
         */
        private function __construct() {
            $this->mte_init_hooks();
        }
        
        /**
         * Obtenir l'instance unique (Singleton)
         * @return Majesty_Text_Effect_Plugin
         */
        public static function mte_get_instance() {
            if (null === self::$mte_instance) {
                self::$mte_instance = new self();
            }
            return self::$mte_instance;
        }
        
        /**
         * Initialiser les hooks WordPress
         */
        private function mte_init_hooks() {
            add_action('wp_enqueue_scripts', array($this, 'mte_enqueue_frontend_assets'));
            add_shortcode('majesty-text-effect', array($this, 'mte_render_shortcode'));
            add_action('admin_menu', array($this, 'mte_add_admin_menu'));
            add_action('admin_init', array($this, 'mte_register_settings'));
            add_action('wp_head', array($this, 'mte_add_inline_styles'), 10);
            add_action('wp_footer', array($this, 'mte_add_inline_scripts'), 10);
            
            // Hook de sécurité supplémentaire
            add_action('init', array($this, 'mte_security_headers'));
        }
        
        /**
         * Ajouter des headers de sécurité
         */
        public function mte_security_headers() {
            // Empêcher l'énumération des utilisateurs via REST API si nécessaire
            if (is_admin() && !current_user_can($this->mte_required_capability)) {
                // Aucune action supplémentaire nécessaire ici pour ce plugin
            }
        }
        
        /**
         * Enregistrer les assets frontend (vide car inline)
         */
        public function mte_enqueue_frontend_assets() {
            // Les styles et scripts sont ajoutés inline pour plus de sécurité
            // et pour éviter les conflits de fichiers
        }
        
        /**
         * Ajouter les styles CSS inline dans le header
         */
        public function mte_add_inline_styles() {
            // Vérifier si le shortcode est utilisé dans la page
            global $post;
            if (is_a($post, 'WP_Post') && !has_shortcode($post->post_content, 'majesty-text-effect')) {
                return; // Ne pas charger si le shortcode n'est pas utilisé
            }
            
            ?>
            <style id="majesty-text-effect-css">
/* Majesty Text Effect - Styles v<?php echo esc_attr(MTE_VERSION); ?> */
.mte-wrapper {
    display: inline-block;
    min-height: 1.5em;
    position: relative;
}

.mte-text {
    display: inline;
}

.mte-cursor {
    display: inline-block;
    animation: mte-blink 1s step-end infinite;
    margin-left: 2px;
}

@keyframes mte-blink {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
}

/* Style Typewriter */
.mte-style-typewriter {
    font-family: 'Courier New', Courier, monospace;
    letter-spacing: 0.05em;
}

/* Style Modern */
.mte-style-modern {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-weight: 300;
    letter-spacing: 0.02em;
}

/* Style Classic */
.mte-style-classic {
    font-family: 'Georgia', 'Times New Roman', serif;
    letter-spacing: 0.01em;
}

/* Style Neon */
.mte-style-neon {
    font-family: 'Arial', sans-serif;
    font-weight: bold;
    text-shadow: 0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor;
}

.mte-style-neon .mte-cursor {
    text-shadow: 0 0 10px currentColor, 0 0 20px currentColor;
}

/* Style Minimal */
.mte-style-minimal {
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    font-weight: 200;
    letter-spacing: 0.1em;
}

/* Animation lors de la frappe */
.mte-wrapper.mte-typing .mte-cursor {
    animation: mte-blink 0.5s step-end infinite;
}
            </style>
            <?php
        }
        
        /**
         * Ajouter le JavaScript inline dans le footer
         */
        public function mte_add_inline_scripts() {
            // Vérifier si le shortcode est utilisé dans la page
            global $post;
            if (is_a($post, 'WP_Post') && !has_shortcode($post->post_content, 'majesty-text-effect')) {
                return; // Ne pas charger si le shortcode n'est pas utilisé
            }
            
            ?>
            <script id="majesty-text-effect-js">
(function() {
    'use strict';
    
    // Protection contre l'exécution multiple
    if (window.MajestyTextEffectLoaded) {
        return;
    }
    window.MajestyTextEffectLoaded = true;
    
    document.addEventListener('DOMContentLoaded', function() {
        const typingElements = document.querySelectorAll('.mte-text');
        
        if (!typingElements.length) {
            return;
        }
        
        typingElements.forEach(function(element) {
            try {
                const sentencesAttr = element.getAttribute('data-mte-sentences');
                if (!sentencesAttr) {
                    return;
                }
                
                const sentences = JSON.parse(sentencesAttr);
                const typeSpeed = parseInt(element.getAttribute('data-mte-type-speed')) || 100;
                const backSpeed = parseInt(element.getAttribute('data-mte-back-speed')) || 50;
                const startDelay = parseInt(element.getAttribute('data-mte-start-delay')) || 500;
                const backDelay = parseInt(element.getAttribute('data-mte-back-delay')) || 4000;
                const loop = element.getAttribute('data-mte-loop') === 'true';
                
                // Validation des valeurs
                if (!Array.isArray(sentences) || sentences.length === 0) {
                    return;
                }
                
                if (typeSpeed < 0 || backSpeed < 0 || startDelay < 0 || backDelay < 0) {
                    return;
                }
                
                let sentenceIndex = 0;
                let charIndex = 0;
                let isDeleting = false;
                let isWaiting = false;
                let timeoutId = null;
                
                const wrapper = element.closest('.mte-wrapper');
                if (!wrapper) {
                    return;
                }
                
                function type() {
                    if (isWaiting) {
                        return;
                    }
                    
                    const currentSentence = sentences[sentenceIndex];
                    
                    if (!isDeleting && charIndex <= currentSentence.length) {
                        // Utiliser textContent pour la sécurité (évite XSS)
                        element.textContent = currentSentence.substring(0, charIndex);
                        charIndex++;
                        wrapper.classList.add('mte-typing');
                        
                        if (charIndex > currentSentence.length) {
                            wrapper.classList.remove('mte-typing');
                            if (loop || sentenceIndex < sentences.length - 1) {
                                isWaiting = true;
                                timeoutId = setTimeout(function() {
                                    isWaiting = false;
                                    isDeleting = true;
                                    type();
                                }, backDelay);
                            }
                        } else {
                            timeoutId = setTimeout(type, typeSpeed);
                        }
                    } else if (isDeleting && charIndex >= 0) {
                        element.textContent = currentSentence.substring(0, charIndex);
                        charIndex--;
                        wrapper.classList.add('mte-typing');
                        
                        if (charIndex < 0) {
                            wrapper.classList.remove('mte-typing');
                            isDeleting = false;
                            sentenceIndex++;
                            
                            if (sentenceIndex >= sentences.length) {
                                if (loop) {
                                    sentenceIndex = 0;
                                } else {
                                    return;
                                }
                            }
                            
                            charIndex = 0;
                            isWaiting = true;
                            timeoutId = setTimeout(function() {
                                isWaiting = false;
                                type();
                            }, 500);
                        } else {
                            timeoutId = setTimeout(type, backSpeed);
                        }
                    }
                }
                
                // Démarrer l'animation
                timeoutId = setTimeout(function() {
                    type();
                }, startDelay);
                
                // Nettoyer les timeouts si l'élément est supprimé
                element.setAttribute('data-mte-initialized', 'true');
                
            } catch (error) {
                // Silencieusement ignorer les erreurs pour ne pas casser la page
                console.error('Majesty Text Effect Error:', error);
            }
        });
    });
})();
            </script>
            <?php
        }
        
        /**
         * Sanitiser une couleur hexadécimale
         * @param string $color
         * @return string
         */
        private function mte_sanitize_hex_color($color) {
            if (empty($color)) {
                return '#000000';
            }
            
            // Supprimer le # si présent
            $color = ltrim($color, '#');
            
            // Vérifier le format hexadécimal
            if (preg_match('/^[a-fA-F0-9]{6}$/', $color)) {
                return '#' . $color;
            } elseif (preg_match('/^[a-fA-F0-9]{3}$/', $color)) {
                return '#' . $color;
            }
            
            return '#000000'; // Couleur par défaut si invalide
        }
        
        /**
         * Sanitiser la taille de police
         * @param string $size
         * @return string
         */
        private function mte_sanitize_font_size($size) {
            // Autoriser uniquement les valeurs CSS valides
            if (preg_match('/^[0-9.]+(?:px|em|rem|%)$/', $size)) {
                return $size;
            }
            return '1.2em'; // Valeur par défaut
        }
        
        /**
         * Sanitiser le style
         * @param string $style
         * @return string
         */
        private function mte_sanitize_style($style) {
            $allowed_styles = array('typewriter', 'modern', 'classic', 'neon', 'minimal');
            return in_array($style, $allowed_styles, true) ? $style : 'typewriter';
        }
        
        /**
         * Sanitiser un nombre positif
         * @param mixed $value
         * @param int $default
         * @return int
         */
        private function mte_sanitize_positive_int($value, $default = 100) {
            $value = intval($value);
            return ($value > 0 && $value < 10000) ? $value : $default;
        }
        
        /**
         * Sanitiser le texte des phrases
         * @param string $text
         * @return string
         */
        private function mte_sanitize_sentences($text) {
            // Supprimer tous les tags HTML et scripts
            $text = strip_tags($text);
            // Supprimer les caractères dangereux
            $text = str_replace(array('<', '>', '"', "'"), '', $text);
            return sanitize_text_field($text);
        }
        
        /**
         * Sanitiser les styles CSS personnalisés
         * @param string $css
         * @return string
         */
        private function mte_sanitize_custom_css($css) {
            if (empty($css)) {
                return '';
            }
            
            // Supprimer les caractères dangereux
            $css = strip_tags($css);
            
            // Liste noire de propriétés dangereuses
            $dangerous_properties = array(
                'behavior', 'expression', 'javascript:', 'vbscript:', 
                '@import', 'binding', '-moz-binding'
            );
            
            foreach ($dangerous_properties as $dangerous) {
                $css = str_ireplace($dangerous, '', $css);
            }
            
            // Limiter la longueur
            $css = substr($css, 0, 500);
            
            return $css;
        }
        
        /**
         * Render le shortcode
         * @param array $atts
         * @return string
         */
        public function mte_render_shortcode($atts) {
            // Sanitiser et valider tous les attributs
            $atts = shortcode_atts(array(
                'sentences' => 'Texte par défaut',
                'style' => 'typewriter',
                'type_speed' => 100,
                'back_speed' => 50,
                'start_delay' => 500,
                'back_delay' => 4000,
                'text_color' => '#000000',
                'cursor_color' => '#000000',
                'loop' => 'false',
                'cursor_char' => '|',
                'font_size' => '1.2em',
                'font_family' => 'Courier New, monospace',
                'custom_text_style' => '',
                'custom_cursor_style' => '',
                'text_class' => '',
                'cursor_class' => ''
            ), $atts, 'majesty-text-effect');
            
            // Sanitisation complète de tous les paramètres
            $sentences_raw = $this->mte_sanitize_sentences($atts['sentences']);
            $style = $this->mte_sanitize_style($atts['style']);
            $type_speed = $this->mte_sanitize_positive_int($atts['type_speed'], 100);
            $back_speed = $this->mte_sanitize_positive_int($atts['back_speed'], 50);
            $start_delay = $this->mte_sanitize_positive_int($atts['start_delay'], 500);
            $back_delay = $this->mte_sanitize_positive_int($atts['back_delay'], 4000);
            $text_color = $this->mte_sanitize_hex_color($atts['text_color']);
            $cursor_color = $this->mte_sanitize_hex_color($atts['cursor_color']);
            $loop = ($atts['loop'] === 'true' || $atts['loop'] === '1') ? 'true' : 'false';
            $cursor_char = substr(sanitize_text_field($atts['cursor_char']), 0, 3);
            $font_size = $this->mte_sanitize_font_size($atts['font_size']);
            $font_family = sanitize_text_field($atts['font_family']);
            
            // Nouveaux paramètres pour les styles personnalisés
            $custom_text_style = $this->mte_sanitize_custom_css($atts['custom_text_style']);
            $custom_cursor_style = $this->mte_sanitize_custom_css($atts['custom_cursor_style']);
            $text_class = sanitize_html_class($atts['text_class']);
            $cursor_class = sanitize_html_class($atts['cursor_class']);
            
            // Séparer les phrases et les sanitiser individuellement
            $sentences = array_map('trim', explode('|', $sentences_raw));
            $sentences = array_filter($sentences); // Supprimer les vides
            
            if (empty($sentences)) {
                return ''; // Ne rien afficher si pas de contenu
            }
            
            // Encoder en JSON de manière sécurisée
            $sentences_json = wp_json_encode($sentences, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            
            if (false === $sentences_json) {
                return ''; // Erreur d'encodage
            }
            
            // ID unique sécurisé
            $unique_id = 'mte-' . wp_generate_password(12, false);
            
            // Construire les styles inline de manière sécurisée
            $inline_style = sprintf(
                'color: %s; font-size: %s; font-family: %s;',
                esc_attr($text_color),
                esc_attr($font_size),
                esc_attr($font_family)
            );
            
            // Ajouter les styles personnalisés pour le texte si fournis
            if (!empty($custom_text_style)) {
                $inline_style .= ' ' . esc_attr($custom_text_style);
            }
            
            $cursor_style = sprintf(
                'color: %s;',
                esc_attr($cursor_color)
            );
            
            // Ajouter les styles personnalisés pour le curseur si fournis
            if (!empty($custom_cursor_style)) {
                $cursor_style .= ' ' . esc_attr($custom_cursor_style);
            }
            
            // Préparer les classes CSS personnalisées
            $text_classes = 'mte-text';
            if (!empty($text_class)) {
                $text_classes .= ' ' . esc_attr($text_class);
            }
            
            $cursor_classes = 'mte-cursor';
            if (!empty($cursor_class)) {
                $cursor_classes .= ' ' . esc_attr($cursor_class);
            }
            
            // Construction sécurisée du HTML
            $output = sprintf(
                '<span id="%s" class="mte-wrapper mte-style-%s" style="%s" data-mte-version="%s">
                    <span class="%s" data-mte-sentences=\'%s\' data-mte-type-speed="%d" data-mte-back-speed="%d" data-mte-start-delay="%d" data-mte-back-delay="%d" data-mte-loop="%s" style="%s"></span><span class="%s" style="%s">%s</span>
                </span>',
                esc_attr($unique_id),
                esc_attr($style),
                esc_attr($inline_style),
                esc_attr(MTE_VERSION),
                esc_attr($text_classes),
                $sentences_json, // Déjà encodé avec wp_json_encode
                absint($type_speed),
                absint($back_speed),
                absint($start_delay),
                absint($back_delay),
                esc_attr($loop),
                esc_attr($inline_style),
                esc_attr($cursor_classes),
                esc_attr($cursor_style),
                esc_html($cursor_char)
            );
            
            return $output;
        }
        
        /**
         * Ajouter le menu d'administration
         */
        public function mte_add_admin_menu() {
            add_options_page(
                esc_html__('Majesty Text Effect Settings', 'majesty-text-effect'),
                esc_html__('Majesty Text Effect', 'majesty-text-effect'),
                $this->mte_required_capability,
                'majesty-text-effect-settings',
                array($this, 'mte_render_settings_page')
            );
        }
        
        /**
         * Enregistrer les paramètres
         */
        public function mte_register_settings() {
            register_setting(
                'mte_settings_group',
                'mte_default_type_speed',
                array(
                    'type' => 'integer',
                    'sanitize_callback' => array($this, 'mte_sanitize_setting_positive_int'),
                    'default' => 100
                )
            );
            
            register_setting(
                'mte_settings_group',
                'mte_default_back_speed',
                array(
                    'type' => 'integer',
                    'sanitize_callback' => array($this, 'mte_sanitize_setting_positive_int'),
                    'default' => 50
                )
            );
        }
        
        /**
         * Callback de sanitisation pour les settings
         */
        public function mte_sanitize_setting_positive_int($value) {
            return $this->mte_sanitize_positive_int($value, 100);
        }
        
        /**
         * Render la page de paramètres
         */
        public function mte_render_settings_page() {
            // Vérification de la capacité
            if (!current_user_can($this->mte_required_capability)) {
                wp_die(esc_html__('Vous n\'avez pas les permissions nécessaires pour accéder à cette page.', 'majesty-text-effect'));
            }
            
            // Vérification du nonce si formulaire soumis
            if (isset($_POST['submit'])) {
                check_admin_referer($this->mte_nonce_action, $this->mte_nonce_name);
            }
            
            ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                
                <div style="background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin: 20px 0;">
                    <h2>📝 <?php esc_html_e('Comment utiliser ce plugin', 'majesty-text-effect'); ?></h2>
                    <p><?php esc_html_e('Utilisez le shortcode suivant dans vos articles ou pages :', 'majesty-text-effect'); ?></p>
                    <code style="background: #f5f5f5; padding: 10px; display: block; margin: 10px 0;">[majesty-text-effect sentences="Votre texte ici"]</code>
                </div>
                
                <hr>
                
                <h3>📋 <?php esc_html_e('Tous les paramètres disponibles :', 'majesty-text-effect'); ?></h3>
                <table class="widefat" style="max-width: 900px; margin: 20px 0;">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Paramètre', 'majesty-text-effect'); ?></th>
                            <th><?php esc_html_e('Description', 'majesty-text-effect'); ?></th>
                            <th><?php esc_html_e('Valeur par défaut', 'majesty-text-effect'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>sentences</strong></td>
                            <td><?php esc_html_e('Le(s) texte(s) à afficher. Utilisez | pour séparer plusieurs phrases', 'majesty-text-effect'); ?></td>
                            <td>"Texte par défaut"</td>
                        </tr>
                        <tr>
                            <td><strong>style</strong></td>
                            <td><?php esc_html_e('Style visuel : typewriter, modern, classic, neon, minimal', 'majesty-text-effect'); ?></td>
                            <td>"typewriter"</td>
                        </tr>
                        <tr>
                            <td><strong>type_speed</strong></td>
                            <td><?php esc_html_e('Vitesse de frappe en millisecondes (plus bas = plus rapide)', 'majesty-text-effect'); ?></td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td><strong>back_speed</strong></td>
                            <td><?php esc_html_e('Vitesse d\'effacement en millisecondes', 'majesty-text-effect'); ?></td>
                            <td>50</td>
                        </tr>
                        <tr>
                            <td><strong>start_delay</strong></td>
                            <td><?php esc_html_e('Délai avant de commencer en millisecondes', 'majesty-text-effect'); ?></td>
                            <td>500</td>
                        </tr>
                        <tr>
                            <td><strong>back_delay</strong></td>
                            <td><?php esc_html_e('Délai avant d\'effacer en millisecondes', 'majesty-text-effect'); ?></td>
                            <td>4000</td>
                        </tr>
                        <tr>
                            <td><strong>text_color</strong></td>
                            <td><?php esc_html_e('Couleur du texte (hexadécimal)', 'majesty-text-effect'); ?></td>
                            <td>#000000</td>
                        </tr>
                        <tr>
                            <td><strong>cursor_color</strong></td>
                            <td><?php esc_html_e('Couleur du curseur (hexadécimal)', 'majesty-text-effect'); ?></td>
                            <td>#000000</td>
                        </tr>
                        <tr>
                            <td><strong>loop</strong></td>
                            <td><?php esc_html_e('Boucle infinie : true ou false', 'majesty-text-effect'); ?></td>
                            <td>false</td>
                        </tr>
                        <tr>
                            <td><strong>cursor_char</strong></td>
                            <td><?php esc_html_e('Caractère du curseur : | _ █ ▮ etc.', 'majesty-text-effect'); ?></td>
                            <td>|</td>
                        </tr>
                        <tr>
                            <td><strong>font_size</strong></td>
                            <td><?php esc_html_e('Taille de police (CSS)', 'majesty-text-effect'); ?></td>
                            <td>1.2em</td>
                        </tr>
                        <tr>
                            <td><strong>font_family</strong></td>
                            <td><?php esc_html_e('Police de caractères (CSS)', 'majesty-text-effect'); ?></td>
                            <td>Courier New, monospace</td>
                        </tr>
                        <tr style="background: #e8f5e9;">
                            <td><strong>custom_text_style</strong></td>
                            <td><?php esc_html_e('Styles CSS personnalisés pour le texte (ex: text-shadow: 2px 2px 4px #000;)', 'majesty-text-effect'); ?></td>
                            <td><?php esc_html_e('vide', 'majesty-text-effect'); ?></td>
                        </tr>
                        <tr style="background: #e8f5e9;">
                            <td><strong>custom_cursor_style</strong></td>
                            <td><?php esc_html_e('Styles CSS personnalisés pour le curseur (ex: background: red; padding: 2px;)', 'majesty-text-effect'); ?></td>
                            <td><?php esc_html_e('vide', 'majesty-text-effect'); ?></td>
                        </tr>
                        <tr style="background: #e8f5e9;">
                            <td><strong>text_class</strong></td>
                            <td><?php esc_html_e('Classe CSS personnalisée pour le texte', 'majesty-text-effect'); ?></td>
                            <td><?php esc_html_e('vide', 'majesty-text-effect'); ?></td>
                        </tr>
                        <tr style="background: #e8f5e9;">
                            <td><strong>cursor_class</strong></td>
                            <td><?php esc_html_e('Classe CSS personnalisée pour le curseur', 'majesty-text-effect'); ?></td>
                            <td><?php esc_html_e('vide', 'majesty-text-effect'); ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <h3>✨ <?php esc_html_e('Exemples d\'utilisation :', 'majesty-text-effect'); ?></h3>
                
                <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                    <h4><?php esc_html_e('Exemple 1 : Style Typewriter basique', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Bienvenue sur mon site!" style="typewriter"]</code>
                </div>
                
                <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                    <h4><?php esc_html_e('Exemple 2 : Avec couleurs personnalisées', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Coco Beach" style="typewriter" type_speed="100" back_speed="50" start_delay="500" back_delay="4000" text_color="#d78a3a" cursor_color="#d78a3a" loop="true"]</code>
                </div>
                
                <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                    <h4><?php esc_html_e('Exemple 3 : Plusieurs phrases en rotation', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Première phrase|Deuxième phrase|Troisième phrase" loop="true" back_speed="30"]</code>
                </div>
                
                <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                    <h4><?php esc_html_e('Exemple 4 : Style moderne avec effets', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Design moderne" style="modern" text_color="#00ff00" cursor_color="#00ff00" font_size="2em"]</code>
                </div>
                
                <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                    <h4><?php esc_html_e('Exemple 5 : Style néon', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Effet néon" style="neon" text_color="#ff00ff" cursor_color="#ff00ff"]</code>
                </div>
                
                <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                    <h4><?php esc_html_e('Exemple 6 : Curseur personnalisé', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Curseur underscore" cursor_char="_"]</code>
                </div>
                
                <div style="background: #e8f5e9; padding: 15px; margin: 10px 0; border-left: 4px solid #4caf50;">
                    <h4>🎨 <?php esc_html_e('Exemple 7 : Styles CSS personnalisés pour le texte', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Texte avec ombre" custom_text_style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5); font-weight: bold;"]</code>
                </div>
                
                <div style="background: #e8f5e9; padding: 15px; margin: 10px 0; border-left: 4px solid #4caf50;">
                    <h4>🎨 <?php esc_html_e('Exemple 8 : Curseur avec arrière-plan personnalisé', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Curseur stylé" cursor_char=" " custom_cursor_style="background: #ff0000; width: 10px; height: 20px; display: inline-block;"]</code>
                </div>
                
                <div style="background: #e8f5e9; padding: 15px; margin: 10px 0; border-left: 4px solid #4caf50;">
                    <h4>🎨 <?php esc_html_e('Exemple 9 : Utilisation de classes CSS personnalisées', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Mon texte" text_class="ma-classe-texte" cursor_class="ma-classe-curseur"]</code>
                    <p style="margin-top: 10px;"><em><?php esc_html_e('Note: Vous devez définir .ma-classe-texte et .ma-classe-curseur dans votre CSS de thème', 'majesty-text-effect'); ?></em></p>
                </div>
                
                <div style="background: #e8f5e9; padding: 15px; margin: 10px 0; border-left: 4px solid #4caf50;">
                    <h4>🎨 <?php esc_html_e('Exemple 10 : Combinaison complète avec styles personnalisés', 'majesty-text-effect'); ?></h4>
                    <code>[majesty-text-effect sentences="Super effet!" style="modern" text_color="#ff6b6b" cursor_color="#4ecdc4" custom_text_style="text-transform: uppercase; letter-spacing: 3px;" custom_cursor_style="background: linear-gradient(45deg, #ff6b6b, #4ecdc4); padding: 0 5px; border-radius: 3px;" font_size="2em" loop="true"]</code>
                </div>
                
                <hr>
                
                <h3>🎨 <?php esc_html_e('Styles disponibles :', 'majesty-text-effect'); ?></h3>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><strong>typewriter</strong> : <?php esc_html_e('Style machine à écrire classique', 'majesty-text-effect'); ?></li>
                    <li><strong>modern</strong> : <?php esc_html_e('Style moderne et épuré', 'majesty-text-effect'); ?></li>
                    <li><strong>classic</strong> : <?php esc_html_e('Style classique avec sérif', 'majesty-text-effect'); ?></li>
                    <li><strong>neon</strong> : <?php esc_html_e('Style néon lumineux avec glow', 'majesty-text-effect'); ?></li>
                    <li><strong>minimal</strong> : <?php esc_html_e('Style minimaliste', 'majesty-text-effect'); ?></li>
                </ul>
                
                <hr>
                
                <div style="background: #fff3cd; padding: 15px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #ffc107;">
                    <h3>🎨 <?php esc_html_e('Guide des styles personnalisés', 'majesty-text-effect'); ?></h3>
                    <p><?php esc_html_e('Vous pouvez maintenant ajouter vos propres styles CSS directement dans le shortcode :', 'majesty-text-effect'); ?></p>
                    
                    <h4><?php esc_html_e('Propriétés CSS recommandées pour le texte :', 'majesty-text-effect'); ?></h4>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li><code>text-shadow</code> : <?php esc_html_e('Ombre portée du texte', 'majesty-text-effect'); ?></li>
                        <li><code>font-weight</code> : <?php esc_html_e('Épaisseur de la police', 'majesty-text-effect'); ?></li>
                        <li><code>text-transform</code> : <?php esc_html_e('Transformation du texte (uppercase, lowercase)', 'majesty-text-effect'); ?></li>
                        <li><code>letter-spacing</code> : <?php esc_html_e('Espacement entre les lettres', 'majesty-text-effect'); ?></li>
                        <li><code>line-height</code> : <?php esc_html_e('Hauteur de ligne', 'majesty-text-effect'); ?></li>
                        <li><code>opacity</code> : <?php esc_html_e('Transparence', 'majesty-text-effect'); ?></li>
                    </ul>
                    
                    <h4><?php esc_html_e('Propriétés CSS recommandées pour le curseur :', 'majesty-text-effect'); ?></h4>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li><code>background</code> : <?php esc_html_e('Couleur ou dégradé d\'arrière-plan', 'majesty-text-effect'); ?></li>
                        <li><code>padding</code> : <?php esc_html_e('Espacement interne', 'majesty-text-effect'); ?></li>
                        <li><code>border-radius</code> : <?php esc_html_e('Arrondi des coins', 'majesty-text-effect'); ?></li>
                        <li><code>width / height</code> : <?php esc_html_e('Dimensions personnalisées', 'majesty-text-effect'); ?></li>
                        <li><code>box-shadow</code> : <?php esc_html_e('Ombre portée', 'majesty-text-effect'); ?></li>
                        <li><code>transform</code> : <?php esc_html_e('Transformation (rotation, scale)', 'majesty-text-effect'); ?></li>
                    </ul>
                    
                    <h4><?php esc_html_e('💡 Astuce : Utilisation de classes CSS', 'majesty-text-effect'); ?></h4>
                    <p><?php esc_html_e('Pour des styles plus complexes, créez des classes CSS dans votre thème et utilisez les paramètres text_class et cursor_class :', 'majesty-text-effect'); ?></p>
                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px;">
/* Dans votre fichier style.css du thème */
.mon-texte-anime {
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.mon-curseur-custom {
    background: #4ecdc4;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}
                    </pre>
                    <p><?php esc_html_e('Puis utilisez :', 'majesty-text-effect'); ?></p>
                    <code>[majesty-text-effect sentences="Texte" text_class="mon-texte-anime" cursor_class="mon-curseur-custom"]</code>
                </div>
                
                <hr>
                
                <div style="background: #d7f0ff; padding: 15px; margin: 20px 0; border-radius: 5px;">
                    <p><strong>💡 <?php esc_html_e('Astuce :', 'majesty-text-effect'); ?></strong> <?php esc_html_e('Vous pouvez combiner plusieurs paramètres pour créer des effets uniques !', 'majesty-text-effect'); ?></p>
                    <p><strong>👨‍💻 <?php esc_html_e('Créé par :', 'majesty-text-effect'); ?></strong> FRANKY MBIELEU</p>
                    <p><strong>🔐 <?php esc_html_e('Version :', 'majesty-text-effect'); ?></strong> <?php echo esc_html(MTE_VERSION); ?></p>
                </div>
                
                <div style="background: #fff3cd; padding: 15px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #ffc107;">
                    <h3>🔒 <?php esc_html_e('Sécurité', 'majesty-text-effect'); ?></h3>
                    <p><?php esc_html_e('Ce plugin a été développé avec les meilleures pratiques de sécurité WordPress :', 'majesty-text-effect'); ?></p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li><?php esc_html_e('Protection contre les accès directs', 'majesty-text-effect'); ?></li>
                        <li><?php esc_html_e('Sanitisation complète de tous les paramètres', 'majesty-text-effect'); ?></li>
                        <li><?php esc_html_e('Protection contre les injections XSS', 'majesty-text-effect'); ?></li>
                        <li><?php esc_html_e('Validation des données utilisateur', 'majesty-text-effect'); ?></li>
                        <li><?php esc_html_e('Utilisation de nonces pour les formulaires', 'majesty-text-effect'); ?></li>
                        <li><?php esc_html_e('Échappement de toutes les sorties', 'majesty-text-effect'); ?></li>
                        <li><?php esc_html_e('Préfixes sur toutes les fonctions et classes', 'majesty-text-effect'); ?></li>
                        <li><?php esc_html_e('Pattern Singleton pour éviter les conflits', 'majesty-text-effect'); ?></li>
                    </ul>
                </div>
            </div>
            <?php
        }
    }
}

/**
 * Fonction d'initialisation du plugin
 * @return Majesty_Text_Effect_Plugin
 */
function mte_initialize_plugin() {
    return Majesty_Text_Effect_Plugin::mte_get_instance();
}

// Initialiser le plugin
add_action('plugins_loaded', 'mte_initialize_plugin');

/**
 * Hook d'activation du plugin
 */
register_activation_hook(__FILE__, function() {
    // Vérifier la version PHP minimale
    if (version_compare(PHP_VERSION, '7.2', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html__('Ce plugin nécessite PHP 7.2 ou supérieur.', 'majesty-text-effect'),
            esc_html__('Erreur d\'activation du plugin', 'majesty-text-effect'),
            array('back_link' => true)
        );
    }
    
    // Vérifier la version WordPress minimale
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html__('Ce plugin nécessite WordPress 5.0 ou supérieur.', 'majesty-text-effect'),
            esc_html__('Erreur d\'activation du plugin', 'majesty-text-effect'),
            array('back_link' => true)
        );
    }
    
    // Définir les options par défaut de manière sécurisée
    add_option('mte_default_type_speed', 100, '', 'no');
    add_option('mte_default_back_speed', 50, '', 'no');
    add_option('mte_default_text_color', '#000000', '', 'no');
    add_option('mte_default_cursor_color', '#000000', '', 'no');
    add_option('mte_plugin_version', MTE_VERSION, '', 'no');
    
    // Flush les rewrite rules (bonne pratique)
    flush_rewrite_rules();
});

/**
 * Hook de désactivation du plugin
 */
register_deactivation_hook(__FILE__, function() {
    // Flush les rewrite rules
    flush_rewrite_rules();
    
    // Note: On ne supprime pas les options pour permettre la réactivation
    // sans perdre les paramètres
});

/**
 * Hook de désinstallation du plugin
 */
register_uninstall_hook(__FILE__, 'mte_uninstall_plugin');

/**
 * Fonction de désinstallation
 */
function mte_uninstall_plugin() {
    // Vérifier les permissions
    if (!current_user_can('activate_plugins')) {
        return;
    }
    
    // Vérifier le referer
    check_ajax_referer('mte-uninstall');
    
    // Supprimer toutes les options du plugin
    delete_option('mte_default_type_speed');
    delete_option('mte_default_back_speed');
    delete_option('mte_default_text_color');
    delete_option('mte_default_cursor_color');
    delete_option('mte_plugin_version');
    
    // Nettoyer les transients si utilisés
    delete_transient('mte_cache');
    
    // Supprimer les options pour tous les sites en multisite
    if (is_multisite()) {
        global $wpdb;
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            delete_option('mte_default_type_speed');
            delete_option('mte_default_back_speed');
            delete_option('mte_default_text_color');
            delete_option('mte_default_cursor_color');
            delete_option('mte_plugin_version');
            delete_transient('mte_cache');
            restore_current_blog();
        }
    }
}

/**
 * Ajouter un lien vers les paramètres dans la liste des plugins
 */
add_filter('plugin_action_links_' . MTE_PLUGIN_BASENAME, function($links) {
    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('options-general.php?page=majesty-text-effect-settings')),
        esc_html__('Paramètres', 'majesty-text-effect')
    );
    array_unshift($links, $settings_link);
    return $links;
});

/**
 * Ajouter des métadonnées dans la liste des plugins
 */
add_filter('plugin_row_meta', function($links, $file) {
    if (MTE_PLUGIN_BASENAME === $file) {
        $row_meta = array(
            'docs' => sprintf(
                '<a href="%s" target="_blank">%s</a>',
                esc_url('https://example.com/docs'),
                esc_html__('Documentation', 'majesty-text-effect')
            ),
            'support' => sprintf(
                '<a href="%s" target="_blank">%s</a>',
                esc_url('https://example.com/support'),
                esc_html__('Support', 'majesty-text-effect')
            )
        );
        return array_merge($links, $row_meta);
    }
    return $links;
}, 10, 2);

/**
 * Ajouter un message après l'activation
 */
add_action('admin_notices', function() {
    $screen = get_current_screen();
    
    if ('plugins' === $screen->id && isset($_GET['activate'])) {
        $plugin_data = get_plugin_data(__FILE__);
        
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php echo esc_html($plugin_data['Name']); ?></strong> 
                <?php esc_html_e('a été activé avec succès !', 'majesty-text-effect'); ?>
                <a href="<?php echo esc_url(admin_url('options-general.php?page=majesty-text-effect-settings')); ?>">
                    <?php esc_html_e('Voir la documentation', 'majesty-text-effect'); ?>
                </a>
            </p>
        </div>
        <?php
    }
});

/**
 * Charger les traductions
 */
add_action('init', function() {
    load_plugin_textdomain(
        'majesty-text-effect',
        false,
        dirname(MTE_PLUGIN_BASENAME) . '/languages/'
    );
});
