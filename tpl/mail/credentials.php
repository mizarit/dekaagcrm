<style type="text/css">
body {
  background: #fff;
}

p {
  width: 740px;
  margin: 0 10px 15px 10px;
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
}

td {
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
  padding: 0;
  margin: 0;
}

table {
  margin: 10px;
  border-collapse: collapse;
  padding: 0;
}

strong {
  color: #00a5e2;;
  font-weight: bold;
}

h1 {
  font-family: "trebuchet ms",helvetica,sans-serif;
  width: 750px;
  margin: 10px 10px 15px 10px;
  border-bottom: #cecece 1px dotted;
  color: #00a5e2;
  font-weight: normal;
  letter-spacing: -1px;
  font-size: 24px;
}

a {
  color: #00a5e2;;
  font-weight: bold;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
}
</style>
<img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/wp-content/plugins/dekaagcrm/data/logo-dekaag-invoice.jpg" alt="">
<h1><?php echo __('Your account credentials', 'dekaagcrm'); ?></h1>
<p>Geachte <?php echo $relation->title; ?>,</p>
<p>Hieronder staan uw inloggegevens voor De Kaag Watersport. Met deze inloggegevens kunt u toegang krijgen tot uw reserveringen, facturen en betalingen.</p>
<table>
  <tr>
    <td style="width: 110px;">Gebruikersnaam:</td>
    <td><strong><?php echo $username; ?></strong></td>
  </tr>
  <tr>
    <td style="width: 110px;">Wachtwoord:</td>
    <td><strong><?php echo $password; ?></strong></td>
  </tr>
</table>
<p>Met vriendelijke groet,</p> 
<p><?php echo $sender_name; ?></p>
<p><br>Deze e-mail is automatisch verzonden op <?php echo date('d-m-Y'); ?>.</p>
