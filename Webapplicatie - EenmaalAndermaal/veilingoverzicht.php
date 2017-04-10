<?php
require_once "includes/header.php";

$isSearch = false;
$search = '';

if(isset($_GET['search'])) { 
	$isSearch = true;
	$search = $_GET['search'];
	$veilingenSQL = "SELECT * FROM Voorwerp WHERE actief = 1 AND titel LIKE '%" . $search . "%' ORDER BY looptijd_einde ASC, titel ASC";
} else {
	if(!isset($_GET['rubriek'])) {
		Redirect::to('index.php');
	}
	$rubriek = $_GET['rubriek'];
	// Haalt alle voorwerpen op die voorkomen in de rubriek en de subrubrieken
	$veilingenSQL = "DECLARE @Id int = {$rubriek}
					;WITH cte AS 
					(
						SELECT a.rubrieknummer, a.hoofdrubrieknummer, a.rubrieknaam
						FROM Rubriek a
						WHERE rubrieknummer = @Id
						UNION ALL
						SELECT a.rubrieknummer, a.hoofdrubrieknummer, a.rubrieknaam
						FROM Rubriek a JOIN cte c ON a.hoofdrubrieknummer = c.rubrieknummer
					)
					SELECT *
					FROM cte c
					inner join Voorwerp_In_Rubriek vw on c.rubrieknummer = vw.rubriek_op_laagste_niveau
					inner join Voorwerp v on vw.voorwerpnummer = v.voorwerpnummer
					WHERE veiling_gesloten = 0
					AND actief = 1
					ORDER BY looptijd_einde DESC, titel ASC";
	
	$getRubrieknummer = database::getInstance()->query("SELECT rubrieknummer FROM Rubriek WHERE rubrieknaam = '{$rubriek}'");
	$getRubrieknummer = $getRubrieknummer->results();
}


?>

<h1><?= ($isSearch) ? 'Resultaten: \'' . $search . '\'' : 'Overzicht veilingen' ?></h1>

<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
		<div class="jumbotron sidebar-rubrieken">		
			<?php
			if(!$isSearch) {
				$rubrieken = database::getInstance()->query("SELECT * FROM Rubriek WHERE hoofdrubrieknummer = " . $_GET['rubriek']);
				echo "<h4>Subrubrieken</h4>";
				echo "<ul>";
				if($rubrieken->count() == 0) {
					echo 'Er zijn geen subrubrieken';
				} else {
					foreach($rubrieken->results() as $rubriek) {
						echo "<li>";
						echo "<a href='veilingoverzicht.php?rubriek=" . $rubriek->rubrieknummer . "'>" . $rubriek->rubrieknaam . "</a>";
						echo "</li>";
					}
				}
				echo "</ul>";
			} else {
				echo "<h4>Zoekresultaten</h4>";
			}
			?>
		</div>
	</div>
	<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
		<?php
		$veilingen = database::getInstance()->query($veilingenSQL);

		if(!$veilingen->count() > 0) {
			echo "<h3>Helaas. Er zijn geen veilingen die hierbij overeen komen.</h3>";
		} else {
		?>
			<div class="row">
				<?php 
				foreach ($veilingen->results() as $veiling) {
					$bestand = database::getInstance()->query("SELECT * FROM Bestand WHERE voorwerpnummer = " . $veiling->voorwerpnummer . "");
					$bestand = $bestand->first();

					$prijs = $veiling->huidig_bod;
					if($prijs == NULL) {
						$prijs = $veiling->start_prijs;
					}
					?>
					<div id="veiling-<?= $veiling->voorwerpnummer ?>" class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
						<div class="panel panel-default veiling-panel">
							<div class="panel-body">
								<img src="<?= $bestand->filenaam ?>" alt='<?= $bestand->filenaam ?>' class="img-responsive">
								<p class="title"><?php $veilingTitel = htmlentities($veiling->titel, ENT_QUOTES, 'UTF-8'); echo truncate($veilingTitel, 50); ?></p>
								<p class="price">&euro; <?= $prijs ?></p>
								<p class="countdown" id="countdown-<?= $veiling->voorwerpnummer ?>">
									<script>initializeClock(
										'countdown-<?= $veiling->voorwerpnummer ?>',
										'<?= timerFormat($veiling->looptijd_einde) ?>'
									)
									</script> 
								</p>
								<a href="veiling.php?id=<?= $veiling->voorwerpnummer ?>" class="btn btn-warning btn-block">Bekijk veiling</a>
							</div>
						</div>
					</div>
					<?php 
				}
				?>
			</div>
		<?php
		}
		?>
	</div>
</div>
<?php
include "includes/footer.php";
?>
