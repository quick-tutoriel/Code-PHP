<?php
/* Plugin Name: Personnalisation Wordpress 
Description: Ensemble des fonctions globales de mon blog. 
Version: 1.0
License: GPL 
Author: Guillaume REYNAUD
Author URI: https://quick-tutoriel.com/ 
*/ 

#Désactivation de la vérification de l'email de l'administrateur
add_filter('admin_email_check_interval', '__return_false');

# Appliquer un CSS personnalisé pour l'administration de Wordpress
# Avec une fonte plus lisible
add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    body, td, textarea, input, select {
      font-family: "Lucida Grande";
      font-size: 12px;
    } 
  </style>';
}
