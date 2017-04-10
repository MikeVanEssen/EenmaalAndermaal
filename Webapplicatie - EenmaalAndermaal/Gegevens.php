<?php
include 'includes/header.php';
require_once 'core/init.php';

if(Input::exists()) {
  if(Token::check(Input::get('token'))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, array(
    'voornaam'      => array(
      'required'      => true,
      'min'       => 2,
      'max'       => 20,
      'ISO-8859-1'    => true,
      'alphachars_only' => true
      ),
    'tussenvoegsel'   => array(
      'min'       => 1,
      'max'       => 15,
      'ISO-8859-1'    => true,
      'alphachars_only' => true
      ),
    'achternaam'    => array(
      'required'      => true,
      'min'       => 2,
      'max'       => 20,
      'ISO-8859-1'    => true,
      'alphachars_only' => true
      ),
    'geboorte_dag'    => array(
      'required'  => true,
      'min'   => 10,
      'ISO-8859-1'=> true,
      'date'    => true,
      '18jaar'  => true
      ),
    'adresregel1'   => array(
      'required'      => true,
      'min'       => 2,
      'max'       => 50,
      'ISO-8859-1'    => true,
      'hasletter&number'  => true
      ),
    'adresregel2'     => array(
      'min'       => 2,
      'max'       => 50,
      'ISO-8859-1'    => true,
      'hasletter&number'  => true
      ),
    'postcode'      => array(
      'required'      => true,
      'postcode'      => true,
      'hasletter&number'  => true,
      'ISO-8859-1'    => true
      ),
    'plaatsnaam'    => array(
      'required'      => true,
      'min'       => 2,
      'max'       => 50,
      'ISO-8859-1'    => true,
      'alphachars_only' => true
      ),
    'land'        => array(
      'required'      => true,
      'min'       => 3,
      'max'       => 32,
      'ISO-8859-1'    => true,
      'alphachars_only' => true
      ),
    'telefoonnummer' => array(
      'required'    => true,
      'max'     => 15,
      'ISO-8859-1'  => true,
      'vast_nummer' => true
      ),
    'mobielnummer' => array(
      'max'     => 15,
      'ISO-8859-1'  => true,
      'mobiel_nummer' => true
      )
    ));
    
    if($validation->passed()) {
      $gebruiker = new Gebruiker();
      $dob = date('Y-m-d', strtotime(Input::get('geboorte_dag')));
      try {
        $gebruiker->create(array(
          'gebruikersnaam'  => Session::get('gebruikersdata')['gebruikersnaam'],
          'voornaam'        => Input::get('voornaam'),
          'tussenvoegsel'   => Input::get('tussenvoegsel'),//wellicht alleen als de input bestaat?
          'achternaam'      => Input::get('achternaam'),
          'adresregel1'     => Input::get('adresregel1'),
          'adresregel2'     => Input::get('adresregel2'),
          'postcode'        => Input::get('postcode'),
          'plaatsnaam'      => Input::get('plaatsnaam'),
          'land'            => Input::get('land'),
          'geboorte_dag'    => $dob,
          'mailbox'         => Session::get('gebruikersdata')['mailbox'],
          'wachtwoord'      => Session::get('gebruikersdata')['wachtwoord'],
          'vraagnummer'     => Session::get('gebruikersdata')['vraagnummer'],
          'antwoordtekst'   => Session::get('gebruikersdata')['antwoordtekst'],
          'soort_gebruiker' => Session::get('gebruikersdata')['soort_gebruiker'],
          'salt'            => Session::get('gebruikersdata')['salt'],
          ));
        Database::getInstance()->insert('Gebruikerstelefoon', array(
          'gebruikersnaam' => Session::get('gebruikersdata')['gebruikersnaam'],
          'telefoonnummer' => Input::get('telefoonnummer'),
          'mobielnummer'   => Input::get('mobielnummer')
          ));
        Redirect::to('Compleet.php');
      } catch(Exception $e) {
        die($e->getMessage());
      }
    } else {
      foreach($validation->getErrors() as $error) {
        Session::put('errors', $validation->getErrors());
      }
    }
  }
}
?>

<h1>Registreren</h1>
<h4> Pagina 2 van de 2</h4>

<form name="persoonsgegevens-form" method="post">
  <p class="text-danger"><?php require_once 'core/init.php'; if(Session::exists('errors')) {foreach(Session::get('errors') as $error) {echo $error, '<br>';}} Session::delete('errors'); ?></p>
  <div class="row account-row">
    <div class="jumbotron account-content col-lg-5 col-md-5 col-sm-12">
      <h4>Persoonsgegevens</h4>
      <div class="form-group">
        <label>Voornaam</label>
        <input type="text" class="form-control" name="voornaam" value="<?= Input::get('voornaam') ?>">
      </div>
      <div class="form-group">
        <label>Tussenvoegsel</label>
        <input type="text" class="form-control" name="tussenvoegsel" value="<?= Input::get('tussenvoegsel') ?>">
      </div>
      <div class="form-group">
        <label>Achternaam</label>
        <input type="text" class="form-control" name="achternaam" value="<?= Input::get('achternaam') ?>">
      </div>
      <div class="form-group">
        <label>Geboortedatum (dd-mm-jjjj)</label>
        <input type="text" class="form-control" name="geboorte_dag" value="<?= Input::get('geboorte_dag') ?>">
      </div>
      <div class="form-group">
        <label>Telefoonnummer</label>
        <input type="text" class="form-control" name="telefoonnummer" value="<?= Input::get('telefoonnummer') ?>">
      </div>
      <div class="form-group">
        <label>Mobiel nummer</label>
        <input type="text" class="form-control" name="mobielnummer" value="<?= Input::get('mobielnummer') ?>">
      </div>
      <div class="form-gap-fill"></div>
    </div>
    <div class="jumbotron account-content col-lg-5 col-lg-offset-1 col-md-5 col-md-offset-1 col-sm-12">
      <h4>Adresgegevens</h4>
      <div class="form-group">
        <label>Adres</label>
        <input type="text" class="form-control" name="adresregel1" value="<?= Input::get('adresregel1') ?>">
        <input type="text" class="form-control" name="adresregel2" value="<?= Input::get('adresregel2') ?>">
      </div>
      <div class="form-group">
        <label>Postcode</label>
        <input type="text" class="form-control" name="postcode" value="<?= Input::get('postcode') ?>">
      </div>
      <div class="form-group">
        <label>Plaatsnaam</label>
        <input type="text" class="form-control" name="plaatsnaam" value="<?= Input::get('plaatsnaam') ?>">
      </div>
      <div class="form-group">
        <label>Land</label>
        <input type="text" class="form-control" name="land" value="<?= Input::get('land') ?>">
      </div>
      <div class="form-gap-fill"></div>
      <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
      <input type="submit" value="Voltooi registratie" class="btn btn-warning pull-right">
      <div class="clearfix"></div>
    </div>
  </div>
</form>    
<?php include "includes/footer.php"; ?>