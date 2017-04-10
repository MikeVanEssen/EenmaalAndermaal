<?php
include 'includes/header.php';
require_once 'core/init.php';

Session::put('previous_page','index.php');

if(Input::exists('post')) {
  if(Token::check(Input::get('token'))) {
  $validate = new Validate();
  $validation = $validate->check($_POST, array(
  'gebruikersnaam'  => array(
    'required'    => true,
    'min'       => 2,
    'max'       => 20,
    'ISO-8859-1'  => true,
    'unique'    => 'gebruiker'
    ),
  'mailbox'       => array(
    'required'    => true,
    'valid_email' => true,
    'ISO-8859-1'  => true,
    'max'     => 254,
    'min'     => 5
    ),
  'wachtwoord'    => array(
    'required'        => true,
    'min'           => 7,
    'max'         => 128,
    'ISO-8859-1'      => true,
    'hasletter&number'  => true
    ),
  'wachtwoord_again'  => array(
    'matches'         => 'wachtwoord',
    'required'      => true,
    'min'         => 7,
    'max'         => 128,
    'ISO-8859-1'      => true,
    'hasletter&number'  => true
    ),
  'vraagnummer'   => array(
    'required'  => true,
    'numeric'   => true
    ),
  'antwoordtekst'   => array(
    'required'    => true,
    'max'     => 25,
    'ISO-8859-1'  => true,
    'alphachars_only' => true
    )
  ));

  if($validation->passed()) {
    $gebruiker = new Gebruiker();
    $salt = Hash::salt(32);

    try {
    $gebruikersdata = array(
      'gebruikersnaam'  => Input::get('gebruikersnaam'),
      'mailbox'         => Input::get('mailbox'),
      'wachtwoord'      => Hash::make(Input::get('wachtwoord'), $salt),
      'vraagnummer'     => Input::get('vraagnummer'),
      'antwoordtekst'   => Input::get('antwoordtekst'),
      'soort_gebruiker' => 'Koper',
      'salt'            => $salt
      );
    Session::put('gebruikersdata', $gebruikersdata);
    Redirect::to('gegevens.php');
    } catch(Exception $e) {
    die($e->getMessage());
    }
  } else {
    Session::put('errors', $validation->getErrors());
  }
  }
}
?>

<h1>Registreren</h1>
<h4> Pagina 1 van de 2</h4>
<div class="row account-row">
  <div class="jumbotron account-content col-lg-12 col-md-12 col-sm-12">
    <h4>Accountgegevens</h4>
    <form name="accountgegevens-form" method="post">
      <p class="text-danger"><?php require_once 'core/init.php'; if(Session::exists('errors')) {foreach(Session::get('errors') as $error) {echo $error, '<br>';}} Session::delete('errors'); ?></p>
      <div class="form-group">
        <label>Emailadres</label>
        <input type="email" class="form-control" name="mailbox" value="<?= Session::get('verified') ?>" readonly><!--Moet read only worden-->
      </div>
      <div class="form-gap-fill"></div>
      <div class="form-group">
          <label>Gebruikersnaam</label>
        <input type="text" class="form-control" name="gebruikersnaam" value="<?= Input::get('gebruikersnaam') ?>">
      </div>
      <div class="form-group">
        <label>Wachtwoord</label>
        <input type="password" class="form-control" name="wachtwoord">
      </div>
      <div class="form-group">
        <label>Herhaal wachtwoord</label>
        <input type="password" class="form-control" name="wachtwoord_again">
      </div>
      <div class="form-gap-fill"></div>
      <div class="form-group">
        <label>Geheime vraag</label>
        <select class="form-control" name="vraagnummer">
      <?php
        require_once 'core/init.php';
        $vragen = Database::getInstance()->query('SELECT * FROM Vraag');
        foreach($vragen->results() as $vraag) {
          echo '<option value="' . $vraag->vraagnummer . '">'. $vraag->tekst_vraag . '</option>';
        }
      ?>
          </select>
        <br>
        <input type="text" class="form-control" name="antwoordtekst" value="<?= Input::get('antwoordtekst') ?>">
      </div>
      <div class="form-gap-fill"></div>
        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
        <input type="submit" value="Volgende stap" class="btn btn-warning pull-right">
    </form>
  </div>
</div>
<?php include "includes/footer.php"; ?>
