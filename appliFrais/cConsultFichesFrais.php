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
  
  // acquisition des données entrées, ici le numéro de mois et l'étape du traitement
  $moisSaisi=lireDonneePost("lstMois", "");
  $etape=lireDonneePost("etape",""); 

  if ($etape != "demanderConsult" && $etape != "validerConsult") {
      // si autre valeur, on considère que c'est le début du traitement
      $etape = "demanderConsult";        
  } 
  if ($etape == "validerConsult") { // l'utilisateur valide ses nouvelles données
                
      // vérification de l'existence de la fiche de frais pour le mois demandé
      $existeFicheFrais = existeFicheFrais($idConnexion, $moisSaisi, obtenirIdUserConnecte());
      // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
      if ( !$existeFicheFrais ) {
          ajouterErreur($tabErreurs, "Le mois demandé est invalide");
      }
      else {
          // récupération des données sur la fiche de frais demandée
          $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, obtenirIdUserConnecte());
      }
  }                                  
?>
  <!-- Division principale -->
  <div id="contenu">
      <h2>Mes fiches de frais</h2>
      <h3>Mois à sélectionner : </h3>
      <form action="" method="post">
      <div class="corpsForm">
          <input type="hidden" name="etape" value="validerConsult" />
      <p>
        <label for="lstMois">Mois : </label>
        <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais">
            <?php
                // on propose tous les mois pour lesquels le visiteur a une fiche de frais
                $req = obtenirReqMoisFicheFrais(obtenirIdUserConnecte());
                $idJeuMois = mysqli_query($idConnexion,$req);
                $lgMois = mysqli_fetch_assoc($idJeuMois);
                while ( is_array($lgMois) ) {
                    $mois = $lgMois["mois"];
                    $noMois = intval(substr($mois, 4, 2));
                    $annee = intval(substr($mois, 0, 4));
            ?>    
            <option value="<?php echo $mois; ?>"<?php if ($moisSaisi == $mois) { ?> selected="selected"<?php } ?>><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
            <?php
                    $lgMois = mysqli_fetch_assoc($idJeuMois);        
                }
                mysqli_free_result($idJeuMois);
            ?>
        </select>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20"
               title="Demandez à consulter cette fiche de frais" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p> 
      </div>
        
      </form>
<?php      

// demande et affichage des différents éléments (forfaitisés et non forfaitisés)
// de la fiche de frais demandée, uniquement si pas d'erreur détecté au contrôle
    if ( $etape == "validerConsult" ) {
        if ( nbErreurs($tabErreurs) > 0 ) {
            echo toStringErreurs($tabErreurs) ;
        }
        else {
?>
    <h3>Fiche de frais du mois de <?php echo obtenirLibelleMois(intval(substr($moisSaisi,4,2))) . " " . substr($moisSaisi,0,4); ?> : 
    <em><?php echo $tabFicheFrais["libelleEtat"]; ?> </em>
    depuis le <em><?php echo $tabFicheFrais["dateModif"]; ?></em></h3>
    <div class="encadre">
    <p>Montant validé : <?php echo $tabFicheFrais["montantValide"] ;
        ?>              
    </p>
<?php          
            // demande de la requête pour obtenir la liste des éléments 
            // forfaitisés du visiteur connecté pour le mois demandé
            $req = obtenirReqEltsForfaitFicheFrais($idConnexion,$moisSaisi, obtenirIdUserConnecte());
            $idJeuEltsFraisForfait = mysqli_query($idConnexion,$req);
            echo mysqli_error($idConnexion);
            $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
            // parcours des frais forfaitisés du visiteur connecté
            // le stockage intermédiaire dans un tableau est nécessaire
            // car chacune des lignes du jeu d'enregistrements doit être doit être
            // affichée au sein d'une colonne du tableau HTML
            $tabEltsFraisForfait = array();
            while ( is_array($lgEltForfait) ) {
                $tabEltsFraisForfait[$lgEltForfait["libelle"]] = $lgEltForfait["quantite"];
                $lgEltForfait = mysqli_fetch_assoc($idJeuEltsFraisForfait);
            }
            mysqli_free_result($idJeuEltsFraisForfait);
            ?>
  	<table class="listeLegere">
  	   <caption>Quantités des éléments forfaitisés</caption>
        <tr>
            <?php
            // premier parcours du tableau des frais forfaitisés du visiteur connecté
            // pour afficher la ligne des libellés des frais forfaitisés
            foreach ( $tabEltsFraisForfait as $unLibelle => $uneQuantite ) {
            ?>
                <th><?php echo $unLibelle ; ?></th>
            <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            // second parcours du tableau des frais forfaitisés du visiteur connecté
            // pour afficher la ligne des quantités des frais forfaitisés
            foreach ( $tabEltsFraisForfait as $unLibelle => $uneQuantite ) {
            ?>
                <td class="qteForfait"><?php echo $uneQuantite ; ?></td>
            <?php
            }
            ?>
        </tr>
    </table>
  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait - <?php echo $tabFicheFrais["nbJustificatifs"]; ?> justificatifs reçus -
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class="montant">Montant</th>                
             </tr>
<?php          
            // demande de la requête pour obtenir la liste des éléments hors
            // forfait du visiteur connecté pour le mois demandé
            $req = obtenirReqEltsHorsForfaitFicheFrais($idConnexion,$moisSaisi, obtenirIdUserConnecte());
            $idJeuEltsHorsForfait = mysqli_query($idConnexion,$req);
            $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
            
            // parcours des éléments hors forfait 
            while ( is_array($lgEltHorsForfait) ) {
            ?>
                <tr>
                   <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                   <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                   <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
                </tr>
            <?php
                $lgEltHorsForfait = mysqli_fetch_assoc($idJeuEltsHorsForfait);
            }
            mysqli_free_result($idJeuEltsHorsForfait);
  ?>
    </table>
  </div>
<?php
        }
    }
?>    
  </div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 
