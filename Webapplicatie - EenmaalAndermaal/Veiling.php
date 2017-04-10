<?php 

include "includes/header.php";

$get = $_GET['id'];

$alertTitel = 'Fout bij comment plaatsen.';
$alertTekst = 'U heeft geen tekst geplaatst of geen waardering gegeven.';

if($gebruiker->isLoggedIn()){
	$logged = '';
} else {
	alert(true, 'warning', 'Let op!', 'Voordat u kunt bieden moet u inloggen.');
	$logged = 'disabled';
} 

$alertBieden = 'Fout bij het bieden';
$alertBiedenTekst = 'U moet een hoger bod invoeren dan het huidige bod';

if($gebruiker->isLoggedIn()){
	$bodplaatsen = 'placeholder="Plaats Bod"';
	$commentplaatsen = 'placeholder="Plaats comment"';
} else {
	$bodplaatsen = 'placeholder="Log in om een bod te plaatsen" disabled';
	$commentplaatsen = 'placeholder="Log in om een comment te plaatsen" disabled';
}

$veiling = database::getInstance()->query("SELECT * FROM Voorwerp WHERE voorwerpnummer = {$get}");
$veiling = $veiling->first();

if (isset($_POST['bieden'])) {
	if ($_POST['bodbedrag'] > $veiling->huidig_bod && $_POST['bodbedrag'] > $veiling->start_prijs && $_POST['bodbedrag'] <= 9999999 && $gebruiker->isLoggedIn()){
		$validate = new Validate();
		$validate->check($_POST, array(
            'bodbedrag' => array(
                'required' => true,
                'numeric' => true
            ),
        ));
        if($validate->passed()) {
        	$bieden = database::getInstance()->insert('bod', array(
			'voorwerpnummer' => $_GET['id'],
			'bodbedrag'=> $_POST['bodbedrag'],
			'gebruikersnaam' => $gebruiker->gegevens()->gebruikersnaam)
			);
			Redirect::to('veiling.php?id=' . $get);
        } else {
        	alert(true, 'danger', 'Foutmeldingen', $validate->getErrors());
        }
	} elseif ($_POST['bodbedrag'] > 9999999) {
		alert(true, 'danger', 'Maximale Bod overschreden', 'Het maximale bod dat geplaatst kan worden is 9.999.999.');
	}else {
		alert(true, 'danger', $alertBieden,$alertBiedenTekst);
	}
}

$laatsteBod = database::getInstance()->query("SELECT * FROM Bod WHERE voorwerpnummer = {$get} ORDER BY bod_datum DESC");
if($laatsteBod->count() > 0) {
	$laatsteBod = $laatsteBod->first();
	if($gebruiker->isLoggedIn()) {
		if($laatsteBod->gebruikersnaam == $gebruiker->gegevens()->gebruikersnaam) {
			$bodplaatsen = 'placeholder="U kunt niet twee keer achter elkaar bieden" disabled';
			$logged = 'disabled';
		}
	}
}

if($veiling->actief == 0) {
	if($veiling->verkoper == $gebruiker->gegevens()->gebruikersnaam) {
		alert(false, 'danger', 'Inactief', 'Uw veiling is niet actief en dus niet beschikbaar voor andere bezoekers.');
	} else {
		Redirect::to('index.php');
	}
}

$bestand = database::getInstance()->get('Bestand', array('voorwerpnummer', '=', $_GET['id']));
$bestand = $bestand->first();

if(Input::exists() && isset($_POST['submitcomment'])) {
	$validate = new Validate();
	$validate->check($_POST, array(
		'commentaar' => array(
		'max' => 400 
		)
	));
	if($validate->passed()) {
		if (!empty($_POST['commentaar']) && $gebruiker->isLoggedIn() && isset($_POST['feedbacksoort'])){
			$commentaarplaatsen= database::getInstance()->insert('feedback',array(
				'voorwerpnummer' => $_GET['id'],
				'gebruikersnaam' => $gebruiker->gegevens()->gebruikersnaam,
				'commentaar' => $_POST['commentaar'],
				'feedbacksoort' => $_POST['feedbacksoort'])
			);
			header('Location: veiling.php?id=' . $get);
		} else {
			alert(true, 'danger', $alertTitel,$alertTekst);
		}
	} else {
		$errorMessage = $validate->getErrors();
	}
}

if ($gebruiker->isLoggedIn() && $veiling->verkoper == $gebruiker->gegevens()->gebruikersnaam) {
	alert(true, 'warning', 'Let op!', 'U kunt niet bieden op uw eigen veiling');
	$bodplaatsen = 'placeholder="U kunt niet op uw eigen veiling bieden" disabled';
	$logged = 'disabled';
}

$bod = $veiling->huidig_bod;
?>

<h1><?= $veiling->titel; ?></h1>

<?php
if(isset($errorMessage)) {
	echo '<div class="col-lg-8 col-md-10">';
	alert(false, 'danger', 'Foutmeldingen:', $errorMessage);
	echo '</div>';
}
?>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-8 col-lg-7">
		<small>Aanbieder: <?= $veiling->verkoper ?></small>
		<img src="<?php echo $bestand->filenaam ?>" alt="<?php echo $veiling->titel;?>" class="img-responsive center-block" >
		<div class="panel panel-default">
			<div class="panel-body veiling">
				<p>
					<?= $veiling->beschrijving; ?> 
				</p>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-body comment radio-toolbar">
				<form method="post">
					<div class="form-group">
						<textarea id="commentaar" name="commentaar" class="form-control" <?php  echo $commentplaatsen ?>><?= (Input::exists('post')) ? Input::get('commentaar') : null?></textarea>
					</div>
					<div class="form-group">
						<button type="submit" name="submitcomment" class="btn btn-success pull-right"><span class="glyphicon glyphicon-plus"></span> Nieuwe comment</button>
						<input type="radio" id="positief" name="feedbacksoort" value="positief">
						<label for="positief"> 
							<a class="btn btn-success" >
								<span class="glyphicon glyphicon-thumbs-up" ></span>
							</a>
						</label>
						<input type="radio" id="negatief" name="feedbacksoort" value="negatief">
						<label for="negatief"> 
							<a class="btn btn-danger" >
								<span class="glyphicon glyphicon-thumbs-down" ></span>
							</a>
						</label>
					</div>
				</form>
			</div>
		</div>
		<?php
		$feedback= database::getInstance()->query("SELECT * FROM Feedback WHERE voorwerpnummer = {$get} AND actief = 1 ORDER BY datum DESC");
		$i=0;
		foreach ($feedback->results() as $comments) {
			if($i < 3) {
			$condition = 'group active';
			}else{
				$condition = 'group';
			}?>
			<div class="<?php echo $condition ?>">
				<div class="panel panel-default " id="group<?php echo $i ?>">
					<div class="panel-body comment">
						<div class="col-xs-0 col-md-3 col-lg-3">
							<p><?= $comments->gebruikersnaam ?></p> 
							<p><?php        
								if($comments->feedbacksoort == 'positief'){
									echo "<a class='btn btn-success disabled'><span class='glyphicon glyphicon-thumbs-up' ></span> </a>";
								} else {
									echo "<a class='btn btn-danger disabled'><span class='glyphicon glyphicon-thumbs-down' ></span> </a>";
								}
								?>
							</p>
						</div>
						<div class="col-xs-12 col-md-12 col-lg-9">
							<?php
							echo $comments->commentaar;
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
			$i++;
		} // end of foreach
		?>
		<a class ="btn btn-default knop" id="load-more">Laat meer comments zien</a>
	</div>
	<div class="col-xs-12 col-sm-5 col-md-4 col-lg-5">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<p class="headerbod">Bieden</p>
			</div>
			<div class="panel-body hoogstebod">
				<h3 class="koptekst">Hoogste bod:</h3>
				<p class="centertekst">&euro;<?php 
					if ($bod == NULL){
						echo $veiling->start_prijs;
					}else {
						echo $veiling->huidig_bod;
					} ?>
				</p>
				<br>
				<h3 class="koptekst">Resterende tijd:</h3>
				<p class="centertekst" id="countdown">
					<script>
						initializeClock('countdown','<?= timerFormat($veiling->looptijd_einde) ?>')
					</script>
				</p>
			</div>
			<div class="panel-body veiltijd">
				<form method="post">
					<div class="form-group">
						<!--  <label for="Bieding">Bod:</label> -->
						<input id="bodbedrag" type="text" name="bodbedrag" class="form-control" <?php echo $bodplaatsen ?>>
					</div>
					<button type="submit" name="bieden" class="btn btn-default btn-lg btn-block knop" <?php echo $logged ?>>Bieden</button>
				</form>
			</div>
			<div class="panel-body">
				<table class="table" >
					<thead>
						<tr>
							<th>Gebruikersnaam</th>
							<th>Bod</th>
						</tr>
						<?php
						$bieding = database::getInstance()->query("SELECT * FROM Bod WHERE voorwerpnummer = {$get} AND actief = 1 ORDER BY bodbedrag DESC");
						foreach ($bieding->results() as $bodgeschiedenis) {  
							?>
							<tbody>
								<tr>
									<td><?= $bodgeschiedenis->gebruikersnaam; ?></td>
									<td>€<?= $bodgeschiedenis->bodbedrag; ?></td>
								</tr>
							</tbody>
							<?php
						} // end foreach
						?>   
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
var $group = $('.group');

$("#load-more").click(function() {
	if ($(this).hasClass('disable')) return false;

	var $hidden = $group.filter(':hidden:first').addClass('active');
	if (!$hidden.next('.group').length) {
		$(this).addClass('disabled');
	}
});
</script>

<?php include "includes/footer.php"; ?>