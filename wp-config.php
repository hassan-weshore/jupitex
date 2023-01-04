<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'testdb' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'RKV&~Jp7Px(Ttw|KN]Rt <ZOoqlgy1=L<ZO19*N^)[tk#aw:;a1=E~s1lVUo#)GA' );
define( 'SECURE_AUTH_KEY',  '3v8<!nX4_TaI}c)DELla)yanwNEq<$}$]^I Ic4v+Z*@B@Jrda-/GFRkO+X{>?^f' );
define( 'LOGGED_IN_KEY',    'G9+BA5B0&Miv,kPOkq =mISyv:pk|)j$p< &pm`<t-0pblsI?PybgK}ASX&wj Ad' );
define( 'NONCE_KEY',        'p$>?uTX(]CV#R5l!v<}m![Rezn=6dQCEOVIPeF}zfjQ&d)BkDi:=~d|#R^8Q)2*l' );
define( 'AUTH_SALT',        'az#0y^n|x|cGMK.ji8Gfr/)WD5P!F27`F|-^Oz;^ntz$>:)hNG_cH^rxVLcKI]Qv' );
define( 'SECURE_AUTH_SALT', '%4JQ4UU`jy{EuArQi.^4/^v +QEP`n$bb6dI4(sbfrP5@{nn@!D>*Twk41,R*1@[' );
define( 'LOGGED_IN_SALT',   'MB{Lp+TWVplJ1+J=43ou|2P/qG=u]_%.&Ez=!%*>/~(Og;70ZAm]]1do[mB*@bnU' );
define( 'NONCE_SALT',       'EU&}v~oVnP-;A`2OnBi^4$+tN1;7L ^S^o-N6:%9~Zk=h|NEZ?k3jyg3o0e#jT#}' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'jup_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
