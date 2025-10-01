# Majesty Text Effect

**Contributors:** frankymbieleu  
**Plugin URI:** https://github.com/frankymbieleu/Majesty-Text-Effect 

**Tags:** effet texte, machine à écrire, animation, shortcode, typewriter, typing effect, curseur clignotant, neon, minimal  
**Requires at least:** 5.0  
**Tested up to:** 6.6  
**Stable tag:** 1.0.0  
**Requires PHP:** 7.0  
**License:**   #

**License URI:**  # 

Ajoutez un effet de machine à écrire avec curseur clignotant à vos textes. Choisissez parmi 5 styles visuels et personnalisez facilement via un shortcode.

## Description

Majesty Text Effect est un plugin WordPress qui apporte une animation de frappe dynamique avec un curseur clignotant à vos pages et articles. Avec 5 styles prédéfinis (typewriter, modern, classic, neon, minimal) et des options de personnalisation comme la vitesse, les couleurs et la police, ce plugin est parfait pour dynamiser vos en-têtes, slogans ou appels à l’action. Compatible avec Gutenberg, Elementor et tous les thèmes WordPress.

### Fonctionnalités principales :
- Animation de frappe avec curseur clignotant personnalisable.
- Support pour plusieurs phrases en rotation (séparées par |).
- 5 styles visuels : typewriter, modern, classic, neon, minimal.
- Options de personnalisation : vitesse de frappe/effacement, délais, couleurs, police, taille de police, caractère du curseur.
- Scripts et styles chargés inline pour une performance optimale.
- Page de paramètres dans l’administration pour configurer les valeurs par défaut.
- Aucun fichier externe requis, assurant une compatibilité maximale.

Ajoutez une touche d’élégance et de dynamisme à votre site dès aujourd’hui !

## Installation

1. Téléchargez le fichier ZIP du plugin.
2. Dans l’administration WordPress, allez dans **Extensions > Ajouter > Téléverser l’extension**.
3. Sélectionnez le fichier ZIP et cliquez sur "Installer maintenant".
4. Activez le plugin.
5. Utilisez le shortcode `[majesty-text-effect]` dans vos articles, pages ou widgets.

### Installation manuelle :
1. Décompressez le fichier ZIP dans le dossier `/wp-content/plugins/`.
2. Activez le plugin via le menu **Extensions** dans WordPress.
3. Accédez aux paramètres dans **Réglages > Majesty Text Effect** pour configurer les options par défaut.

## Utilisation

Utilisez le shortcode suivant dans vos articles, pages ou widgets :

```markdown
[majesty-text-effect sentences="Votre texte ici"]
```

### Paramètres disponibles :

| Paramètre      | Description                                                                 | Valeur par défaut    |
|----------------|-----------------------------------------------------------------------------|----------------------|
| sentences      | Texte(s) à afficher. Séparez plusieurs phrases par \|                       | "Texte par défaut"   |
| style          | Style visuel : typewriter, modern, classic, neon, minimal                   | "typewriter"         |
| type_speed     | Vitesse de frappe en millisecondes (plus bas = plus rapide)                 | 100                  |
| back_speed     | Vitesse d’effacement en millisecondes                                       | 50                   |
| start_delay    | Délai avant de commencer en millisecondes                                   | 500                  |
| back_delay     | Délai avant d’effacer en millisecondes                                      | 4000                 |
| text_color     | Couleur du texte (hexadécimal)                                              | #000000              |
| cursor_color   | Couleur du curseur (hexadécimal)                                            | #000000              |
| loop           | Boucle infinie : true ou false                                              | false                |
| cursor_char    | Caractère du curseur : \| _ █ ▮ etc.                                       | \|                   |
| font_size      | Taille de police (CSS)                                                      | 1.2em                |
| font_family    | Police de caractères (CSS)                                                  | Courier New, monospace |

### Exemples d’utilisation :

**Exemple 1 : Style Typewriter basique**  
```markdown
[majesty-text-effect sentences="Bienvenue sur mon site!" style="typewriter"]
```

**Exemple 2 : Couleurs personnalisées**  
```markdown
[majesty-text-effect sentences="Coco Beach" style="typewriter" type_speed="100" back_speed="50" text_color="#d78a3a" cursor_color="#d78a3a" loop="true"]
```

**Exemple 3 : Plusieurs phrases en rotation**  
```markdown
[majesty-text-effect sentences="Première phrase|Deuxième phrase|Troisième phrase" loop="true" back_speed="30"]
```

**Exemple 4 : Style moderne**  
```markdown
[majesty-text-effect sentences="Design moderne" style="modern" text_color="#00ff00" cursor_color="#00ff00" font_size="2em"]
```

**Exemple 5 : Style néon**  
```markdown
[majesty-text-effect sentences="Effet néon" style="neon" text_color="#ff00ff" cursor_color="#ff00ff"]
```

**Exemple 6 : Curseur personnalisé**  
```markdown
[majesty-text-effect sentences="Curseur underscore" cursor_char="_"]
```

### Styles disponibles :
- **typewriter** : Style machine à écrire classique.
- **modern** : Style épuré et moderne.
- **classic** : Style élégant avec police à empattement.
- **neon** : Effet lumineux avec ombre portée (glow).
- **minimal** : Style minimaliste et léger.

**Astuce :** Combinez les paramètres pour créer des effets uniques ! Vous pouvez également ajouter du CSS personnalisé en ciblant la classe `.majesty-text-effect-wrapper`.

## Foire aux questions

### Le plugin charge-t-il des fichiers externes ?
Non, tous les styles CSS et scripts JavaScript sont intégrés inline pour une performance optimale et une compatibilité maximale.

### Puis-je utiliser plusieurs instances sur la même page ?
Oui, chaque instance du shortcode utilise un ID unique pour éviter les conflits.

### Est-il compatible avec les page builders comme Elementor ou Gutenberg ?
Oui, le plugin fonctionne parfaitement avec Gutenberg, Elementor, ou tout autre éditeur supportant les shortcodes WordPress.

### Comment ajouter plusieurs phrases ?
Utilisez le caractère `|` pour séparer les phrases dans l’attribut `sentences`, par exemple : `sentences="Phrase 1|Phrase 2"`.

### Que faire si l’animation ne démarre pas ?
Vérifiez que JavaScript est activé sur votre site et qu’il n’y a pas de conflits avec d’autres plugins. Essayez avec un thème par défaut (ex. Twenty Twenty-Four) pour isoler le problème.

### Puis-je personnaliser davantage l’apparence ?
Oui, utilisez du CSS personnalisé en ciblant les classes `.majesty-text-effect-wrapper`, `.majesty-text-effect-text` ou `.majesty-text-effect-cursor`.

## Captures d'écran

1. Page de paramètres dans l’administration WordPress.
2. Exemple d’effet typewriter sur une page.
3. Exemple d’effet neon avec couleurs personnalisées.
4. Exemple de rotation de plusieurs phrases.

## Journal des modifications

### 1.0.0
- Version initiale avec effet de frappe, curseur clignotant et 5 styles visuels.
- Ajout du shortcode `[majesty-text-effect]` avec options de personnalisation.
- Page de paramètres pour configurer les valeurs par défaut.

## Remarques de mise à niveau

### 1.0.0
Première version, aucune mise à niveau nécessaire.

## Crédits

Créé par FRANKY MBIELEU.  
Pour plus d’informations, contactez l’auteur via frankymbieleu@gmal.com.
