<?php
/**
 * Script de contrôle et d'affichage du cas d'utilisation "Consulter une fiche de frais"
 * @package default
 * @todo  RAS
 */
$repInclude = './include/';
require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
if ( ! estVisiteurConnecte() ) {
  header("Location: cSeConnecter.php");  
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
                                 
?>
<!-- Division principale -->
<div id="contenu">
  <h2>Praticiens</h2>
        
</div>
<?php        
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>