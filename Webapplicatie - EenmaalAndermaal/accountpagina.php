<?php
include "includes/header.php";
require_once 'core/init.php';

$gebruiker = new Gebruiker();
if(!$gebruiker->isLoggedIn()) {
  Redirect::to('index.php');
}
?>

<h1>Account</h1>

<div class="account-alert">
<?php 
    if(!$gebruiker->heeftToestemming('Verkoper')){
        alert(true ,'success', null, 'Wilt u ook producten op de veiling aanbieden? Upgrade uw account nu naar een verkoopaccount!
        <a href="aanvraag-verkoopaccount.php" class="btn button btn-warning btn-lg">Upgrade account</a>');
    }
?>
</div>

<div class="row account-row">
    <ul class="nav nav-pills nav-stacked col-lg-3 col-md-3 col-sm-3">
    	<li class="active"><a href="#biedingen" data-toggle="pill">Mijn biedingen</a></li>
        <?php
        if($gebruiker->heeftToestemming('Verkoper')) {
            echo '<li><a href="#veilingen" data-toggle="pill">Mijn veilingen</a></li>';
        }
        ?>
        <li class="nav-divider"></li>
    		<li><a href="gegevens_aanpassen.php">Accountgegevens aanpassen</a></li>
        <li><a href="wachtwoord_aanpassen.php">Wachtwoord veranderen</a></li>
	</ul>
	<div class="tab-content jumbotron account-content col-lg-9 col-md-9 col-sm-9">
		<div class="tab-pane active" id="biedingen">
            <h4>Mijn biedingen</h4>
            <?php
            $mijnBiedingen = database::getInstance()->query("SELECT * FROM Voorwerp v
                                                            INNER JOIN Bod b
                                                            ON v.voorwerpnummer = b.voorwerpnummer
                                                            WHERE gebruikersnaam = '{$gebruiker->gegevens()->gebruikersnaam}'
                                                            AND v.actief = 1
                                                            ORDER BY bod_datum DESC");
            $mijnBiedingen = $mijnBiedingen->results();

            if(count($mijnBiedingen) == 0) {
                echo "U heeft geen bod geplaatst";
            } else {
            ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Bod datum</th>
                        <th class="text-right">Uw bod</th>
                        <th class="text-right">Huidig hoogste bod</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($mijnBiedingen as $bod) {
                    ?>
                    <tr>
                        <td><a href="veiling.php?id=<?=$bod->voorwerpnummer?>"><?= $bod->titel ?></a></td>
                        <td><?= date('j-F-Y H:i', strtotime($bod->bod_datum)) ?></td>
                        <td class="text-right">&euro; <?= $bod->bodbedrag ?></td>
                        <td class="text-right">&euro; <?= $bod->huidig_bod ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
            }
            ?>
        </div>
        <?php
        if($gebruiker->heeftToestemming('Verkoper')){
        ?>
        <div class="tab-pane" id="veilingen">
            <h4>Mijn veilingen</h4>
            <a href="veiling-plaatsen.php" class="btn btn-success">Veiling toevoegen</a>
            <?php
            $mijnVeilingen = database::getInstance()->query("SELECT huidig_bod, titel, looptijd_einde, start_prijs, v.voorwerpnummer
                                                                    FROM Voorwerp v
                                                                    LEFT JOIN Bod b
                                                                    ON v.voorwerpnummer = b.voorwerpnummer
                                                                    WHERE verkoper = '{$gebruiker->gegevens()->gebruikersnaam}'
                                                                    AND v.actief = 1
                                                                    GROUP BY huidig_bod, titel, looptijd_einde, start_prijs, v.voorwerpnummer
                                                                    ORDER BY looptijd_einde ASC
                                                                    ");
            $mijnVeilingen = $mijnVeilingen->results();
            if(count($mijnVeilingen) == 0) {
                echo "U heeft geen veilingen geplaatst";
            } else {
            ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Titel</th>
                            <th class="text-right">Einddatum</th>
                            <th class="text-right">Huidig hoogste bod</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($mijnVeilingen as $veiling) {
                            if($veiling->huidig_bod == NULL) {
                                $huidigbod = $veiling->start_prijs;
                            } else {
                                $huidigbod = $veiling->huidig_bod;
                            }
                            ?>
                            <tr>
                                <td><a href="veiling.php?id=<?=$veiling->voorwerpnummer?>"><?= $veiling->titel ?></a></td>
                                <td class="text-right"><?= date('j-F-Y H:i', strtotime($veiling->looptijd_einde)) ?></td>
                                <td class="text-right">&euro; <?= $huidigbod ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            }
            ?>
        </div>
        <?php
        }
        ?>
	</div><!-- tab content -->
</div>
<script type="text/javascript">
	
</script>
<?php include "includes/footer.php"; ?>