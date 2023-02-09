<?php
/*
* Plugin Name: Arrow Keyboard
 * Plugin URI: https://www.cuttalo.com/
   Description: Questo plugin permette di spostarsi tra le pagine, articoli e prodotti sul sito utilizzando le frecce della tastiera.
   Author: Cuttalo srl
 * Version: 1.0.0
 * Domain Path: languages
 * GitHub Plugin URI: vincenzorubino27031980/Arrows-plugin-wordpress

*/

function spostamento_tastiera() {
   ?>
   <script type="text/javascript">
      jQuery(document).ready(function($) {
         $(document).keydown(function(e) {
            if (e.keyCode == 37) {
               // Azione per spostarsi alla pagina, articolo o prodotto precedente
               var post_precedente = getPostPrecedente();
               if (post_precedente) {
                  window.location.href = post_precedente.url;
               }
            } else if (e.keyCode == 39) {
               // Azione per spostarsi alla pagina, articolo o prodotto successivo
               var post_successivo = getPostSuccessivo();
               if (post_successivo) {
                  window.location.href = post_successivo.url;
               }
            }
         });
      });

      function getPostPrecedente() {
         // Recupera tutte le pagine, articoli e prodotti sul sito
         var posts = [];
         <?php
            $args = array(
               'post_type' => array('page', 'post', 'product'),
               'posts_per_page' => -1
            );
            $query = new WP_Query($args);
            while ($query->have_posts()) {
               $query->the_post();
               ?>
               posts.push({
                  tipo: "<?php echo get_post_type(); ?>",
                  titolo: "<?php the_title(); ?>",
                  url: "<?php the_permalink(); ?>"
               });
               <?php
            }
            wp_reset_postdata();
         ?>

         // Ordina i post in base al loro titolo
         posts.sort(function(a, b) {
            return a.titolo.localeCompare(b.titolo);
         });

         // Identifica il post corrente
         var url_corrente = window.location.href;
         for (var i = 0; i < posts.length; i++) {
            if (posts[i].url == url_corrente) {
               if (i > 0) {
                  return posts[i - 1];
               } else {
                  return null;
               }
            }
         }

         return null;
      }

      function getPostSuccessivo() {
         // Recupera tutte le pagine, articoli e prodotti sul sito
         var posts = [];
         <?php
            $args = array(
               'post_type' => array('page', 'post', 'product'),
               'posts_per_page' => -1
            );
            $query = new WP_Query($args);
            while ($query->have_posts()) {
               $query->the_post();
               ?>
               posts.push({
                  tipo: "<?php echo get_post_type(); ?>",
                  titolo: "<?php the_title(); ?>",
                  url: "<?php the_permalink(); ?>"
               });
               <?php
            }
            wp_reset_postdata();
         ?>

         // Ordina i post in base al loro tipo e titolo
         posts.sort(function(a, b) {
            if (a.tipo == b.tipo) {
               return a.titolo.localeCompare(b.titolo);
            } else if (a.tipo == 'post') {
               return -1;
            } else if (a.tipo == 'page' && b.tipo == 'product') {
               return -1;
            } else {
               return 1;
            }
         });

         // Identifica il post corrente
         var url_corrente = window.location.href;
         for (var i = 0; i < posts.length; i++) {
            if (posts[i].url == url_corrente) {
               if (i < posts.length - 1) {
                  return posts[i + 1];
               } else {
                  return null;
               }
            }
         }

         return null;
      }
   </script>
   <?php
}
add_action( 'wp_footer', 'spostamento_tastiera' );
