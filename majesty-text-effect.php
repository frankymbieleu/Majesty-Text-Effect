<?php
/**
 * Plugin Name: Majesty Text Effect
 * Plugin URI: #
 * Description: Effet machine à écrire avec curseur clignotant et multiples styles
 * Version: 1.0.0
 * Author: FRANKY MBIELEU
 * Author URI: #
 * License: GPL v2 or later
 * Text Domain: majesty-text-effect
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

class MajestyTextEffect {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('majesty-text-effect', array($this, 'typing_shortcode'));
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_head', array($this, 'add_inline_styles'));
        add_action('wp_footer', array($this, 'add_inline_scripts'));
    }
    
    // Charger les scripts et styles inline
    public function enqueue_scripts() {
        // Scripts et styles ajoutés inline dans wp_head et wp_footer
    }
    
    // Ajouter le CSS inline
    public function add_inline_styles() {
        ?>
        <style id="majesty-text-effect-css">
/* Styles de base */
.majesty-text-effect-wrapper {
    display: inline-block;
    min-height: 1.5em;
}

.majesty-text-effect-text {
    display: inline;
}

.majesty-text-effect-cursor {
    display: inline-block;
    animation: majesty-blink 1s step-end infinite;
    margin-left: 2px;
}

@keyframes majesty-blink {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
}

/* Style Typewriter */
.majesty-style-typewriter {
    font-family: 'Courier New', Courier, monospace;
    letter-spacing: 0.05em;
}

/* Style Modern */
.majesty-style-modern {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-weight: 300;
    letter-spacing: 0.02em;
}

/* Style Classic */
.majesty-style-classic {
    font-family: 'Georgia', 'Times New Roman', serif;
    letter-spacing: 0.01em;
}

/* Style Neon */
.majesty-style-neon {
    font-family: 'Arial', sans-serif;
    font-weight: bold;
    text-shadow: 0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor;
}

.majesty-style-neon .majesty-text-effect-cursor {
    text-shadow: 0 0 10px currentColor, 0 0 20px currentColor;
}

/* Style Minimal */
.majesty-style-minimal {
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    font-weight: 200;
    letter-spacing: 0.1em;
}

/* Animation lors de la frappe */
.majesty-text-effect-wrapper.typing .majesty-text-effect-cursor {
    animation: majesty-blink 0.5s step-end infinite;
}
        </style>
        <?php
    }
    
    // Ajouter le JavaScript inline
    public function add_inline_scripts() {
        ?>
        <script id="majesty-text-effect-js">
document.addEventListener('DOMContentLoaded', function() {
    const typingElements = document.querySelectorAll('.majesty-text-effect-text');
    
    typingElements.forEach(element => {
        const sentences = JSON.parse(element.getAttribute('data-sentences'));
        const typeSpeed = parseInt(element.getAttribute('data-type-speed')) || 100;
        const backSpeed = parseInt(element.getAttribute('data-back-speed')) || 50;
        const startDelay = parseInt(element.getAttribute('data-start-delay')) || 500;
        const backDelay = parseInt(element.getAttribute('data-back-delay')) || 4000;
        const loop = element.getAttribute('data-loop') === 'true';
        
        let sentenceIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let isWaiting = false;
        
        const wrapper = element.closest('.majesty-text-effect-wrapper');
        
        function type() {
            if (isWaiting) return;
            
            const currentSentence = sentences[sentenceIndex];
            
            if (!isDeleting && charIndex <= currentSentence.length) {
                element.textContent = currentSentence.substring(0, charIndex);
                charIndex++;
                wrapper.classList.add('typing');
                
                if (charIndex > currentSentence.length) {
                    wrapper.classList.remove('typing');
                    if (loop || sentenceIndex < sentences.length - 1) {
                        isWaiting = true;
                        setTimeout(() => {
                            isWaiting = false;
                            isDeleting = true;
                            type();
                        }, backDelay);
                    }
                } else {
                    setTimeout(type, typeSpeed);
                }
            } else if (isDeleting && charIndex >= 0) {
                element.textContent = currentSentence.substring(0, charIndex);
                charIndex--;
                wrapper.classList.add('typing');
                
                if (charIndex < 0) {
                    wrapper.classList.remove('typing');
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
                    setTimeout(() => {
                        isWaiting = false;
                        type();
                    }, 500);
                } else {
                    setTimeout(type, backSpeed);
                }
            }
        }
        
        setTimeout(() => {
            type();
        }, startDelay);
    });
});
        </script>
        <?php
    }
    
    // Shortcode pour l'effet typing
    public function typing_shortcode($atts) {
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
            'font_family' => 'Courier New, monospace'
        ), $atts);
        
        $unique_id = 'majesty-text-effect-' . uniqid();
        
        // Séparer les phrases si plusieurs sont données (séparées par |)
        $sentences = explode('|', $atts['sentences']);
        $sentences_json = json_encode($sentences);
        
        $inline_style = sprintf(
            'color: %s; font-size: %s; font-family: %s;',
            esc_attr($atts['text_color']),
            esc_attr($atts['font_size']),
            esc_attr($atts['font_family'])
        );
        
        $cursor_style = sprintf(
            'color: %s;',
            esc_attr($atts['cursor_color'])
        );
        
        $output = sprintf(
            '<span id="%s" class="majesty-text-effect-wrapper majesty-style-%s" style="%s">
                <span class="majesty-text-effect-text" data-sentences=\'%s\' data-type-speed="%s" data-back-speed="%s" data-start-delay="%s" data-back-delay="%s" data-loop="%s" data-cursor-char="%s"></span><span class="majesty-text-effect-cursor" style="%s">%s</span>
            </span>',
            esc_attr($unique_id),
            esc_attr($atts['style']),
            $inline_style,
            $sentences_json,
            esc_attr($atts['type_speed']),
            esc_attr($atts['back_speed']),
            esc_attr($atts['start_delay']),
            esc_attr($atts['back_delay']),
            esc_attr($atts['loop']),
            esc_attr($atts['cursor_char']),
            $cursor_style,
            esc_html($atts['cursor_char'])
        );
        
        return $output;
    }
    
    // Ajouter page de paramètres
    public function add_settings_page() {
        add_options_page(
            'Majesty Text Effect Settings',
            'Majesty Text Effect',
            'manage_options',
            'majesty-text-effect-settings',
            array($this, 'settings_page_html')
        );
    }
    
    // Enregistrer les paramètres
    public function register_settings() {
        register_setting('majesty_text_effect_settings', 'majesty_default_type_speed');
        register_setting('majesty_text_effect_settings', 'majesty_default_back_speed');
        register_setting('majesty_text_effect_settings', 'majesty_default_text_color');
        register_setting('majesty_text_effect_settings', 'majesty_default_cursor_color');
    }
    
    public function settings_page_html() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div style="background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin: 20px 0;">
                <h2>📝 Comment utiliser ce plugin</h2>
                <p>Utilisez le shortcode suivant dans vos articles ou pages :</p>
                <code style="background: #f5f5f5; padding: 10px; display: block; margin: 10px 0;">[majesty-text-effect sentences="Votre texte ici"]</code>
            </div>
            
            <hr>
            
            <h3>📋 Tous les paramètres disponibles :</h3>
            <table class="widefat" style="max-width: 900px; margin: 20px 0;">
                <thead>
                    <tr>
                        <th>Paramètre</th>
                        <th>Description</th>
                        <th>Valeur par défaut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>sentences</strong></td>
                        <td>Le(s) texte(s) à afficher. Utilisez | pour séparer plusieurs phrases</td>
                        <td>"Texte par défaut"</td>
                    </tr>
                    <tr>
                        <td><strong>style</strong></td>
                        <td>Style visuel : typewriter, modern, classic, neon, minimal</td>
                        <td>"typewriter"</td>
                    </tr>
                    <tr>
                        <td><strong>type_speed</strong></td>
                        <td>Vitesse de frappe en millisecondes (plus bas = plus rapide)</td>
                        <td>100</td>
                    </tr>
                    <tr>
                        <td><strong>back_speed</strong></td>
                        <td>Vitesse d'effacement en millisecondes</td>
                        <td>50</td>
                    </tr>
                    <tr>
                        <td><strong>start_delay</strong></td>
                        <td>Délai avant de commencer en millisecondes</td>
                        <td>500</td>
                    </tr>
                    <tr>
                        <td><strong>back_delay</strong></td>
                        <td>Délai avant d'effacer en millisecondes</td>
                        <td>4000</td>
                    </tr>
                    <tr>
                        <td><strong>text_color</strong></td>
                        <td>Couleur du texte (hexadécimal)</td>
                        <td>#000000</td>
                    </tr>
                    <tr>
                        <td><strong>cursor_color</strong></td>
                        <td>Couleur du curseur (hexadécimal)</td>
                        <td>#000000</td>
                    </tr>
                    <tr>
                        <td><strong>loop</strong></td>
                        <td>Boucle infinie : true ou false</td>
                        <td>false</td>
                    </tr>
                    <tr>
                        <td><strong>cursor_char</strong></td>
                        <td>Caractère du curseur : | _ █ ▮ etc.</td>
                        <td>|</td>
                    </tr>
                    <tr>
                        <td><strong>font_size</strong></td>
                        <td>Taille de police (CSS)</td>
                        <td>1.2em</td>
                    </tr>
                    <tr>
                        <td><strong>font_family</strong></td>
                        <td>Police de caractères (CSS)</td>
                        <td>Courier New, monospace</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>✨ Exemples d'utilisation :</h3>
            
            <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                <h4>Exemple 1 : Style Typewriter basique</h4>
                <code>[majesty-text-effect sentences="Bienvenue sur mon site!" style="typewriter"]</code>
            </div>
            
            <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                <h4>Exemple 2 : Avec couleurs personnalisées</h4>
                <code>[majesty-text-effect sentences="Coco Beach" style="typewriter" type_speed="100" back_speed="50" start_delay="500" back_delay="4000" text_color="#d78a3a" cursor_color="#d78a3a" loop="true"]</code>
            </div>
            
            <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                <h4>Exemple 3 : Plusieurs phrases en rotation</h4>
                <code>[majesty-text-effect sentences="Première phrase|Deuxième phrase|Troisième phrase" loop="true" back_speed="30"]</code>
            </div>
            
            <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                <h4>Exemple 4 : Style moderne avec effets</h4>
                <code>[majesty-text-effect sentences="Design moderne" style="modern" text_color="#00ff00" cursor_color="#00ff00" font_size="2em"]</code>
            </div>
            
            <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                <h4>Exemple 5 : Style néon</h4>
                <code>[majesty-text-effect sentences="Effet néon" style="neon" text_color="#ff00ff" cursor_color="#ff00ff"]</code>
            </div>
            
            <div style="background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">
                <h4>Exemple 6 : Curseur personnalisé</h4>
                <code>[majesty-text-effect sentences="Curseur underscore" cursor_char="_"]</code>
            </div>
            
            <hr>
            
            <h3>🎨 Styles disponibles :</h3>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><strong>typewriter</strong> : Style machine à écrire classique</li>
                <li><strong>modern</strong> : Style moderne et épuré</li>
                <li><strong>classic</strong> : Style classique avec sérif</li>
                <li><strong>neon</strong> : Style néon lumineux avec glow</li>
                <li><strong>minimal</strong> : Style minimaliste</li>
            </ul>
            
            <hr>
            
            <div style="background: #d7f0ff; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <p><strong>💡 Astuce :</strong> Vous pouvez combiner plusieurs paramètres pour créer des effets uniques !</p>
                <p><strong>👨‍💻 Créé par :</strong> FRANKY MBIELEU</p>
            </div>
        </div>
        <?php
    }
}

// Initialiser le plugin
new MajestyTextEffect();

// Définir les options par défaut lors de l'activation
register_activation_hook(__FILE__, function() {
    add_option('majesty_default_type_speed', 100);
    add_option('majesty_default_back_speed', 50);
    add_option('majesty_default_text_color', '#000000');
    add_option('majesty_default_cursor_color', '#000000');
});
?>