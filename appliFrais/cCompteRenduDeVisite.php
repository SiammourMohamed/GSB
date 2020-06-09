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
  <h2>Gestion des comptes rendus</h2>
         <ul id="menuList">
           <li class="smenu">
              <a href="cComptesRendus.php" title="comptes-rendus">comptes-rendus</a>
           </li>
           <p></p>
           <li class="smenu">
              <a href="cVisiteurs.php" title="Visiteurs">Visiteurs</a>
           </li>
           <p></p>
           <li class="smenu">
              <a href="cPraticiens.php" title="Praticiens">Praticiens</a>
           </li>
           <p></p>
           <li class="smenu">
              <a href="cMedicaments.php" title="Medicaments">Medicaments</a>
           </li>
         </ul>
</div>
<?php        
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>