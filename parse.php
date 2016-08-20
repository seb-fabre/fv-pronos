<?php 
session_start(); 

require_once('mysql_connexion.php');
require_once('includes.php');

$date = date('Y-m-d');
$leagueId = GETorPOST('league');
$league = League::find($leagueId);

$users = User::getAll();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $league->name ?></title>
	</head>
	
	<body>
		<?php
			if (!empty($_POST['valider'])) {
			
        $final = array();
			
        // traitement des resultats corrects
        $lignes = explode("\n", nl2br($_POST['resultats']));
        
        $resultats = array();
        foreach($lignes as $l) {
          if (strlen($l) > 8) {
            ereg("([0-9])-([0-9])", $l, $e);
            $do = (int)$e[1];
            $ex = (int)$e[2];
            if ($do>$ex)
              $res = 'v-'.$do.'-'.$ex;
            else if ($do==$ex)
              $res = 'n-'.$do.'-'.$ex;
            else
              $res = 'd-'.$do.'-'.$ex;
            
            $resultats[] = $res;
          }
        }
        
        // traitement des pronos
        $lignes = explode("\n", nl2br($_POST['pronos']));
       
        while($cur = current($lignes)) {
          
          //  le nom du pronostiqueur courant
          if (strpos($cur, 'rono')) {
            $pron = array();
            
            // encore pour sauter les lignes vides �ventuelles
            next($lignes);
            while(strlen(current($lignes))<10)
               next($lignes);
            
            $i=0; // indice de deplacement dans le tableau
            $scores = array();  // sauvegarde des resultats avant affichage
            $trois = 0; // nombre de scores � trois points
            $un = 0;  // nombre de scores � un point
            // parcours des lignes de scores
            while(strlen(current($lignes))>10) {
              if (ereg("([0-9])-([0-9])", current($lignes), $reg)) {
                $do = (int)$reg[1];
                $ex = (int)$reg[2];
                if ($do>$ex)
                  $res = 'v-'.$do.'-'.$ex;
                else if ($do==$ex)
                  $res = 'n-'.$do.'-'.$ex;
                else
                  $res = 'd-'.$do.'-'.$ex;
                
                $maligne = str_replace('<br />', '', current($lignes));
                $maligne = str_replace('arrow.gif', ' : ', $maligne);
                $maligne = ereg_replace('[a-zA-Z0-9]+.gif', '', $maligne);
                
                if (empty($resultats[$i]))
                  $scores[] = $maligne;
                else if ($resultats[$i]==$res) {
                  $trois++;
                  $scores[] = $maligne.' [color=#2E8B57] >>> 3 points  :&#33;: [/color]';
                } else if ($resultats[$i]{0}==$res{0}) {
                  $un++;
                  $scores[] = $maligne.' [color=#2E8B57] >>> 1 point[/color]';
                } else
                  $scores[] = $maligne.' [color=#2E8B57] >>> 0 point[/color]';
                  
                  
               
              } else {  // ligne au format incorrect (ex: "OM-PSG :( match en cours")
                
              }
               
              $i++;
              next($lignes);
            }
            
            $cur = ereg_replace('[a-zA-Z0-9]+.gif', '', $cur);
            
            // affichage des resultats pour le pronostiqueur en cours
            // exemple: "arteau : 5 pts (1, 2)"
			$names = explode(' ', str_replace('<br />', '', $cur));
			$name = '';
			for($i=1; $i<count($names)-2; $i++)
				$name .= $names[$i];
			echo '<b>'.$name.'</b> ';
			echo '<pre>' . var_dump(User::findBy('login', $name)) . '</pre>';
            echo '[b]'.str_replace('<br />', '', $cur).'[color=#FF0000] = '.(3*$trois+$un).' points[/color][/b]<br /><br />'."\n";
            foreach ($scores as $s)
              echo $s.'<br />'."\n";
              
            echo '<br /><br />';
            echo '[color=#000080][b][center]---------------------------------------------------------------------------[/center][/b][/color]';
            echo '<br /><br />'."\n";
          }
            
          next($lignes);
        }
        
        
      } else {
		?>
      <form method="post" action="parse.php">
        <legend>Pronos</legend>
        <p>
          <label>Bons r�sultats</label>
          <textarea name="resultats" cols="40" rows="10"></textarea>
        </p>
        <p>
          <label>Pronos</label>
          <textarea name="pronos" cols="40" rows="10"></textarea>
        </p>
        <p><input type="submit" name="valider" /></p>
      </form>
    <?php
			}
		?>
	</body>
</html>