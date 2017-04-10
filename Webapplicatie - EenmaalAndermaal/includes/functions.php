<?php

// format de datum zodat de javascript timer functie ermee kan werken
// $eindtijd is de tijd die omgezet wordt
function timerFormat($eindtijd) {
	$eindtijd = date("c", strtotime($eindtijd));
	return $eindtijd;
}

// kapt een block text af na een bepaalt aantal tekens en zorgt ervoor dat er niet midden in het woord afgekapt word
// $string is de text
// $length is de lengte die de text maximaal mag hebben
// $append is het teken dat aan het eind van de string komt
function truncate($string,$length=100,$append="&hellip;") {
  $string = trim($string);

  if(strlen($string) > $length) {
    $string = wordwrap($string, $length);
    $string = explode("\n", $string, 2);
    $string = $string[0] . $append;
  }

  return $string;
}

// geeft een melding weer
// dismissable bepaalt of de melding weg te klikken is
// het type bepaalt het kleurgebruik: success = groen; info = blauw; warning = geel; danger = rood;
// zie: http://getbootstrap.com/components/#alerts
function alert($dismissable = false, $type = 'success', $title, $message) {
	echo '<div class="alert alert-' . $type . ' alert-dismissible" role="alert">';

		if($dismissable) {
			echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		}

		if(isset($title)) {
			echo '<h4>' . $title . '</h4>';
		}

		if(is_array($message)) {
			echo '<ul>';
			foreach ($message as $m) {
				echo '<li class="capitalize-first-letter">' . $m . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<span class="capitalize-first-letter">' . $message . '</span>';
		}
		?>
	</div>
	<?php
}

// wanneer de pagina voorkomt in de array zal niet hier naar terug genavigeerd worden na het inloggen
// $blocked_uris is de array met pagina's
function blockedPreviousPages($blocked_uris) {
	// alle values hoofdletters maken om problemen met case sensitive vergelijken te voorkomen
	$blocked_uris = array_map('strtoupper', $blocked_uris);

	if(!in_array(strtoupper($_SERVER['REQUEST_URI']), $blocked_uris)) {
	  Session::put('previous_page', $_SERVER['REQUEST_URI']);
	}
}

// op de meegegeven pagina's worden veilinggegevens opgeslagen, wanneer de pagina niet in de array zit worden deze verwijdered
// $session_uris is de array met pagina's
function saveVeilingData($session_uris) {
	// alle values hoofdletters maken om problemen met case sensitive vergelijken te voorkomen
	$session_uris = array_map('strtoupper', $session_uris); 

	if(!in_array(strtoupper($_SERVER['REQUEST_URI']), $session_uris)) {
	  Session::delete('veilingData');
	}
}

// stuur een mail naar gebruikers met het hoogste bod wanneer een veiling gesloten is
function mailKopers() {
	$getKopers = database::getInstance()->query("SELECT *, v.voorwerpnummer as 'voorwerpId' FROM Voorwerp v
												INNER JOIN Bod b ON b.voorwerpnummer = v.voorwerpnummer
												INNER JOIN Gebruiker g ON g.gebruikersnaam = b.gebruikersnaam
												WHERE v.veiling_gesloten = 1
												AND v.actief = 1
												AND v.Koper <> NULL");

	$headers = "From: noreply@eenmaalandermaal.nl\r\n";
	$headers .= "Reply-To: noreply@eenmaalandermaal.nl\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	$subject = "Winnaar veiling!";

	foreach ($getKopers->results() as $koper) {
		$message = '<html><body>';
			$message .= "<p>Beste " . $koper->gebruikersnaam . ",</p>";
			$message .= "<p>U bent de winnaar van een veiling op EenmaalAndermaal!<br/>Onderstaande is een overzicht van deze veiling met verdere betaling en verzendinstructies:</p>";
			$message .= "<table>";
				$message .= "<tr>";
					$message .= "<td>Titel:</td>";
					$message .= "<td>" . $koper->titel . "</td>";
				$message .= "</tr>";
				$message .= "<tr>";
					$message .= "<td>Bod:</td>";
					$message .= "<td>" . $koper->bodbedrag . "</td>";
				$message .= "</tr>";
				$message .= "<tr>";
					$message .= "<td>Datum:</td>";
					$message .= "<td>" . date('d-m-Y H:i', strtotime($koper->bod_datum)) . "</td>";
				$message .= "</tr>";
				$message .= "<tr>";
					$message .= "<td>Beschrijving:</td>";
					$message .= "<td>" . $koper->beschrijving . "</td>";
				$message .= "</tr>";
				$message .= "<tr>";
					$message .= "<td>Betalingsinstructie:</td>";
					$message .= "<td>" . $koper->betalings_instructie . "</td>";
				$message .= "</tr>";
				$message .= "<tr>";
					$message .= "<td>Plaats:</td>";
					$message .= "<td>" . $koper->plaatsnaam . "</td>";
				$message .= "</tr>";
				$message .= "<tr>";
					$message .= "<td>Land:</td>";
					$message .= "<td>" . $koper->land . "</td>";
				$message .= "</tr>";
				$message .= "<tr>";
					$message .= "<td>Verzendkosten:</td>";
					$message .= "<td>" . $koper->verzendkosten . "</td>";
				$message .= "</tr>";
				$message .= "<tr>";
					$message .= "<td>Verzendinstructies:</td>";
					$message .= "<td>" . $koper->verzend_instructies . "</td>";
				$message .= "</tr>";
			$message .= "</table>";
			$message .= "<p>Met vriendelijke groet,</p>";
			$message .= "<p>EenmaalAndermaal</p>";
		$message .= '</body></html>';
		//die($message);
		mail($koper->mailbox, $subject, $message, $headers);

		$setVoorwerpInactief = database::getInstance()->update('Voorwerp', 'voorwerpnummer', $koper->voorwerpId, array('actief' => 0));
	}
}


// upload een bestand
// $file is het bestand
// $filenaam is de naam die het bestand zal krijgen
// $folder is de locatie waar het bestand naar geupload moet worden
function uploadFile($file, $filename, $folder) {
	$target_dir = $folder;
	$target_file = $target_dir . basename($file['name']);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	$filename = $filename . '.' . $imageFileType;
	// Check if image file is a actual image or fake image
	if(isset($_POST["plaatsen"])) {
	    $check = getimagesize($file["tmp_name"]);
	    if($check !== false) {
	      // alert(false, 'succes', 'Succes', "File is an image - " . $check["mime"] . ".");
	        $uploadOk = 1;
	    } else {
	        alert(false, 'danger', 'Error', 'Dit bestand is geen afbeelding.');
	        $uploadOk = 0;
	    }
	}
	// Check if file already exists
	if (file_exists($target_dir . basename($filename))) {
	   	alert(false, 'danger', 'Error', 'Dit bestand bestaat al');
	    $uploadOk = 0;
	}
	// Check file size
	if ($file["size"] > 500000) { // 500000 = 500kb
	    alert(false, 'danger', 'Error', 'Dit bestand is te groot');
	    $uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "JPG" && $imageFileType != "png" && $imageFileType != "PNG" && $imageFileType != "jpeg" && $imageFileType != "JPEG"
	&& $imageFileType != "gif" && $imageFileType != "GIF" ) {
	    alert(false, 'danger', 'Error', "Alleen JPG, JPEG, PNG & GIF bestanden zijn toegestaan.");
	    $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    alert(false, 'danger', 'Error', 'Het bestand is niet geupload');
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($file["tmp_name"], $target_dir . $filename)) {
	    	//die($target_dir . $filename);
	        alert(false, 'success', 'Success', "Het bestand is geupload.");
	        return $target_dir . $filename;
	    } else {
	        alert(false, 'danger', 'Error', 'Er ging iets mis tijdens het uploaden.');
	    }
	}

	return false;
}