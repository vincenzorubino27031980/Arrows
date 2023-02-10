<?php
/*
* Plugin Name: Arrow Keyboard
 * Plugin URI: https://www.cuttalo.com/
   Description: Questo plugin permette di spostarsi tra le pagine, articoli e prodotti sul sito utilizzando le frecce della tastiera.
   Author: Cuttalo srl
 * Version: 1.0.3
 * GitHub Plugin URI: vincenzorubino27031980/Arrows-plugin-wordpress

*/
         
function spostamento_tastiera()
{
   //escludi l'accesso per gli utenti non amministratori
   if (!current_user_can('manage_options')) {
      return;
   }
   // Recupera tutte le pagine, articoli e prodotti sul sito
   $posts = [];
   $args = array(
      'post_type' => array('page', 'post', 'product'),
      'posts_per_page' => -1
   );
   $query = new WP_Query($args);//esegue la query
   while ($query->have_posts()) {//se ci sono post da mostrare
      $query->the_post();//mostra il post corrente 
      $posts[] = array(//aggiunge un elemento all'array
         'tipo' => get_post_type(),//recupera il tipo di post  
         'titolo' => get_the_title(),//recupera il titolo del post
         'url' => get_the_permalink()//recupera l'url del post
      );
   }
   wp_reset_postdata();

   // Ordina i post in base al loro tipo e titolo (PHP 7+)
   usort($posts, function($a, $b) {
      if ($a['tipo'] == $b['tipo']) {
         return $a['titolo'] <=> $b['titolo'];
      } else if ($a['tipo'] == 'post') {
         return -1;
      } else if ($a['tipo'] == 'page' && $b['tipo'] == 'product') {
         return -1;
      } else {
         return 1;
      }
   });

   // Identifica il post corrente
   $url_corrente = $_SERVER['REQUEST_URI'];
   $post_corrente = null;
   foreach ($posts as $post) {
      if ($post['url'] == $url_corrente) {
         $post_corrente = $post;
         break;
      }
   }

   // Identifica il post precedente
   $post_precedente = null;
   $post_successivo = null;
   if ($post_corrente) {
      $index = array_search($post_corrente, $posts);
      if ($index > 0) {
         $post_precedente = $posts[$index - 1];
      }
      if ($index < count($posts) - 1) {
         $post_successivo = $posts[$index + 1];
      }
   }
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
         var url_corrente = window.location.href;//recupera l'url della pagina corrente
         for (var i = 0; i < posts.length; i++) {//scorre tutti i post
            if (posts[i].url == url_corrente) {//se l'url del post corrente è uguale all'url della pagina corrente
            
               if (i > 0) {//se l'indice è maggiore di 0
                  return posts[i - 1];//restituisce il post precedente
               } else {//altrimenti
                  return null;//restituisce null //non ci sono post precedenti
               }     //
            }
         }

         return null;//restituisce null//non ci sono post precedenti//non è stato trovato il post corrente
      } 

      function getPostSuccessivo() {//funzione per recuperare il post successivo
         // Recupera tutte le pagine, articoli e prodotti sul sito
         var posts = [];//crea un array vuoto
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
