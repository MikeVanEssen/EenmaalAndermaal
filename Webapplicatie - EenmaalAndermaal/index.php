<?php
include "includes/header.php";

$rubrieken = database::getInstance()->query("SELECT * FROM Rubriek WHERE hoofdrubrieknummer IS NULL ORDER BY rubrieknaam");
?>

<h1>Rubrieken</h1>

<div class="row home-categories">
	<?php foreach($rubrieken->results() as $rubriek) {
		$laagsteNiveau = database::getInstance()->query("SELECT * FROM Rubriek WHERE hoofdrubrieknummer = {$rubriek->rubrieknummer}");
		$laagsteNiveau = $laagsteNiveau->results();

		$rubriekLink = 'index.php?rubriek=' . $rubriek->rubrieknaam;
		if(count($laagsteNiveau) == 0) {
			$rubriekLink = 'veilingoverzicht.php?rubriek=' . $rubriek->rubrieknaam;
		}
		?>
		<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
			<div class="cat-wrap">
				<div class="cat-imgblock" style="background:url(<?= $rubriek->filenaam ?>); background-position:center center; background-repeat:no-repeat;"></div>
				<a href="<?= 'veilingoverzicht.php?rubriek=' . $rubriek->rubrieknummer ?>" class="btn btn-lg btn-warning btn-block"><?= $rubriek->rubrieknaam ?></a>
			</div>
		</div>
	<?php } ?>
</div>

<?php include "includes/footer.php"; ?>