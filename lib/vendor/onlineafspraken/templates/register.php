<h2><?php echo __('Uw reservering'); ?></h2>

<div id="appointment-info">
  <?php echo $appointmentInfo; ?>
</div>

<div class="form-button">
	<button type="button" onclick="widget.startBooking();"><?php echo __('Aanpassen'); ?></button>
</div>

<h2><?php echo __('Gebruikers registratie'); ?></h2>
<p><?php echo __('Vul het onderstaande formulier in om uw gebruikersaccount aan te maken. Hierna kunt u uw reservering met dit gebruikersprofiel bevestigen.'); ?></p>

<div id="error-container">
  <ul class="form-errors" id="form-errors">
    <li>&nbsp;</li>
  </ul>
</div>

<form action="#" method="post" id="register-form">
  <fieldset>
    <legend>Registratie formulier</legend>

    <h2><?php echo __('Uw gegevens'); ?></h2>

    <div class="form-row">
      <div class="form-label"><label for="FirstName"><?php echo __('Voornaam'); ?> *</label></div>
      <input type="text" name="FirstName" id="FirstName">
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="Insertions"><?php echo __('Tussenvoegsels'); ?></label></div>
      <input type="text" name="Insertions" id="Insertions" style="width:90px !important;">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="LastName"><?php echo __('Achternaam'); ?> *</label></div>
      <input type="text" name="LastName" id="LastName">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="Street"><?php echo __('Adres'); ?></label></div>
      <input type="text" name="Street" id="Street">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="ZipCode"><?php echo __('Postcode'); ?></label></div>
      <input type="text" name="ZipCode" id="ZipCode" style="width:90px !important;">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="City"><?php echo __('Plaats'); ?></label></div>
      <input type="text" name="City" id="City">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="Email"><?php echo __('E-mail adres'); ?> *</label></div>
      <input type="text" name="Email" id="Email">
    </div>
    
    
    <div class="form-row">
      <div class="form-label"><label for="Country"><?php echo __('Land'); ?> *</label></div>
      <select name="Country" id="Country">
<option value="Afghanistan">Afghanistan</option>
<option value="Alandeilanden">Alandeilanden</option>
<option value="Albanië">Albanië</option>
<option value="Algerije">Algerije</option>
<option value="Amerikaans Samoa">Amerikaans Samoa</option>
<option value="Amerikaanse Maagdeneilanden">Amerikaanse Maagdeneilanden</option>
<option value="Amerikaanse kleinere afgelegen eilanden">Amerikaanse kleinere afgelegen eilanden</option>
<option value="Andorra">Andorra</option>
<option value="Angola">Angola</option>
<option value="Anguilla">Anguilla</option>
<option value="Antarctica">Antarctica</option>
<option value="Antigua en Barbuda">Antigua en Barbuda</option>
<option value="Argentinië">Argentinië</option>
<option value="Armenië">Armenië</option>
<option value="Aruba">Aruba</option>
<option value="Australië">Australië</option>
<option value="Azerbeidzjan">Azerbeidzjan</option>
<option value="Bahama’s">Bahama’s</option>
<option value="Bahrein">Bahrein</option>
<option value="Bangladesh">Bangladesh</option>
<option value="Barbados">Barbados</option>
<option value="België">België</option>
<option value="Belize">Belize</option>
<option value="Benin">Benin</option>
<option value="Bermuda">Bermuda</option>
<option value="Bhutan">Bhutan</option>
<option value="Bolivia">Bolivia</option>
<option value="Bosnië en Herzegovina">Bosnië en Herzegovina</option>
<option value="Botswana">Botswana</option>
<option value="Bouveteiland">Bouveteiland</option>
<option value="Brazilië">Brazilië</option>
<option value="Britse Gebieden in de Indische Oceaan">Britse Gebieden in de Indische Oceaan</option>
<option value="Britse Maagdeneilanden">Britse Maagdeneilanden</option>
<option value="Brunei">Brunei</option>
<option value="Bulgarije">Bulgarije</option>
<option value="Burkina Faso">Burkina Faso</option>
<option value="Burundi">Burundi</option>
<option value="Cambodja">Cambodja</option>
<option value="Canada">Canada</option>
<option value="Caymaneilanden">Caymaneilanden</option>
<option value="Centraal-Afrikaanse Republiek">Centraal-Afrikaanse Republiek</option>
<option value="Chili">Chili</option>
<option value="China">China</option>
<option value="Christmaseiland">Christmaseiland</option>
<option value="Cocoseilanden">Cocoseilanden</option>
<option value="Colombia">Colombia</option>
<option value="Comoren">Comoren</option>
<option value="Congo">Congo</option>
<option value="Congo-Kinshasa">Congo-Kinshasa</option>
<option value="Cookeilanden">Cookeilanden</option>
<option value="Costa Rica">Costa Rica</option>
<option value="Cuba">Cuba</option>
<option value="Cyprus">Cyprus</option>
<option value="Denemarken">Denemarken</option>
<option value="Djibouti">Djibouti</option>
<option value="Dominica">Dominica</option>
<option value="Dominicaanse Republiek">Dominicaanse Republiek</option>
<option value="Duitsland">Duitsland</option>
<option value="Ecuador">Ecuador</option>
<option value="Egypte">Egypte</option>
<option value="El Salvador">El Salvador</option>
<option value="Equatoriaal-Guinea">Equatoriaal-Guinea</option>
<option value="Eritrea">Eritrea</option>
<option value="Estland">Estland</option>
<option value="Ethiopië">Ethiopië</option>
<option value="Faeröer">Faeröer</option>
<option value="Falklandeilanden">Falklandeilanden</option>
<option value="Fiji">Fiji</option>
<option value="Filipijnen">Filipijnen</option>
<option value="Finland">Finland</option>
<option value="Frankrijk">Frankrijk</option>
<option value="Frans-Guyana">Frans-Guyana</option>
<option value="Frans-Polynesië">Frans-Polynesië</option>
<option value="Franse Gebieden in de zuidelijke Indische Oceaan">Franse Gebieden in de zuidelijke Indische Oceaan</option>
<option value="Gabon">Gabon</option>
<option value="Gambia">Gambia</option>
<option value="Georgië">Georgië</option>
<option value="Ghana">Ghana</option>
<option value="Gibraltar">Gibraltar</option>
<option value="Grenada">Grenada</option>
<option value="Griekenland">Griekenland</option>
<option value="Groenland">Groenland</option>
<option value="Guadeloupe">Guadeloupe</option>
<option value="Guam">Guam</option>
<option value="Guatemala">Guatemala</option>
<option value="Guernsey">Guernsey</option>
<option value="Guinee">Guinee</option>
<option value="Guinee-Bissau">Guinee-Bissau</option>
<option value="Guyana">Guyana</option>
<option value="Haïti">Haïti</option>
<option value="Heard- en McDonaldeilanden">Heard- en McDonaldeilanden</option>
<option value="Honduras">Honduras</option>
<option value="Hongarije">Hongarije</option>
<option value="Hongkong SAR van China">Hongkong SAR van China</option>
<option value="IJsland">IJsland</option>
<option value="Ierland">Ierland</option>
<option value="India">India</option>
<option value="Indonesië">Indonesië</option>
<option value="Irak">Irak</option>
<option value="Iran">Iran</option>
<option value="Isle of Man">Isle of Man</option>
<option value="Israël">Israël</option>
<option value="Italië">Italië</option>
<option value="Ivoorkust">Ivoorkust</option>
<option value="Jamaica">Jamaica</option>
<option value="Japan">Japan</option>
<option value="Jemen">Jemen</option>
<option value="Jersey">Jersey</option>
<option value="Jordanië">Jordanië</option>
<option value="Kaapverdië">Kaapverdië</option>
<option value="Kameroen">Kameroen</option>
<option value="Kazachstan">Kazachstan</option>
<option value="Kenia">Kenia</option>
<option value="Kirgizië">Kirgizië</option>
<option value="Kiribati">Kiribati</option>
<option value="Koeweit">Koeweit</option>
<option value="Kroatië">Kroatië</option>
<option value="Laos">Laos</option>
<option value="Lesotho">Lesotho</option>
<option value="Letland">Letland</option>
<option value="Libanon">Libanon</option>
<option value="Liberia">Liberia</option>
<option value="Libië">Libië</option>
<option value="Liechtenstein">Liechtenstein</option>
<option value="Litouwen">Litouwen</option>
<option value="Luxemburg">Luxemburg</option>
<option value="Macao SAR van China">Macao SAR van China</option>
<option value="Macedonië">Macedonië</option>
<option value="Madagaskar">Madagaskar</option>
<option value="Malawi">Malawi</option>
<option value="Maldiven">Maldiven</option>
<option value="Maleisië">Maleisië</option>
<option value="Mali">Mali</option>
<option value="Malta">Malta</option>
<option value="Marokko">Marokko</option>
<option value="Marshalleilanden">Marshalleilanden</option>
<option value="Martinique">Martinique</option>
<option value="Mauritanië">Mauritanië</option>
<option value="Mauritius">Mauritius</option>
<option value="Mayotte">Mayotte</option>
<option value="Mexico">Mexico</option>
<option value="Micronesië">Micronesië</option>
<option value="Moldavië">Moldavië</option>
<option value="Monaco">Monaco</option>
<option value="Mongolië">Mongolië</option>
<option value="Montenegro">Montenegro</option>
<option value="Montserrat">Montserrat</option>
<option value="Mozambique">Mozambique</option>
<option value="Myanmar">Myanmar</option>
<option value="Namibië">Namibië</option>
<option value="Nauru">Nauru</option>
<option value="Nederland" selected="selected">Nederland</option>
<option value="Nederlandse Antillen">Nederlandse Antillen</option>
<option value="Nepal">Nepal</option>
<option value="Nicaragua">Nicaragua</option>
<option value="Nieuw-Caledonië">Nieuw-Caledonië</option>
<option value="Nieuw-Zeeland">Nieuw-Zeeland</option>
<option value="Niger">Niger</option>
<option value="Nigeria">Nigeria</option>
<option value="Niue">Niue</option>
<option value="Noord-Korea">Noord-Korea</option>
<option value="Noordelijke Marianeneilanden">Noordelijke Marianeneilanden</option>
<option value="Noorwegen">Noorwegen</option>
<option value="Norfolkeiland">Norfolkeiland</option>
<option value="Oeganda">Oeganda</option>
<option value="Oekraïne">Oekraïne</option>
<option value="Oezbekistan">Oezbekistan</option>
<option value="Oman">Oman</option>
<option value="Onbekend of onjuist gebied">Onbekend of onjuist gebied</option>
<option value="Oost-Timor">Oost-Timor</option>
<option value="Oostenrijk">Oostenrijk</option>
<option value="Pakistan">Pakistan</option>
<option value="Palau">Palau</option>
<option value="Palestijns Gebied">Palestijns Gebied</option>
<option value="Panama">Panama</option>
<option value="Papoea-Nieuw-Guinea">Papoea-Nieuw-Guinea</option>
<option value="Paraguay">Paraguay</option>
<option value="Peru">Peru</option>
<option value="Pitcairn">Pitcairn</option>
<option value="Polen">Polen</option>
<option value="Portugal">Portugal</option>
<option value="Puerto Rico">Puerto Rico</option>
<option value="Qatar">Qatar</option>
<option value="Roemenië">Roemenië</option>
<option value="Rusland">Rusland</option>
<option value="Rwanda">Rwanda</option>
<option value="Réunion">Réunion</option>
<option value="Saint Barthélemy">Saint Barthélemy</option>
<option value="Saint Kitts en Nevis">Saint Kitts en Nevis</option>
<option value="Saint Lucia">Saint Lucia</option>
<option value="Saint Pierre en Miquelon">Saint Pierre en Miquelon</option>
<option value="Saint Vincent en de Grenadines">Saint Vincent en de Grenadines</option>
<option value="Salomonseilanden">Salomonseilanden</option>
<option value="Samoa">Samoa</option>
<option value="San Marino">San Marino</option>
<option value="Sao Tomé en Principe">Sao Tomé en Principe</option>
<option value="Saoedi-Arabië">Saoedi-Arabië</option>
<option value="Senegal">Senegal</option>
<option value="Servië">Servië</option>
<option value="Servië en Montenegro">Servië en Montenegro</option>
<option value="Seychellen">Seychellen</option>
<option value="Sierra Leone">Sierra Leone</option>
<option value="Singapore">Singapore</option>
<option value="Sint-Helena">Sint-Helena</option>
<option value="Sint-Maarten">Sint-Maarten</option>
<option value="Slovenië">Slovenië</option>
<option value="Slowakije">Slowakije</option>
<option value="Soedan">Soedan</option>
<option value="Somalië">Somalië</option>
<option value="Spanje">Spanje</option>
<option value="Sri Lanka">Sri Lanka</option>
<option value="Suriname">Suriname</option>
<option value="Svalbard en Jan Mayen">Svalbard en Jan Mayen</option>
<option value="Swaziland">Swaziland</option>
<option value="Syrië">Syrië</option>
<option value="Tadzjikistan">Tadzjikistan</option>
<option value="Taiwan">Taiwan</option>
<option value="Tanzania">Tanzania</option>
<option value="Thailand">Thailand</option>
<option value="Togo">Togo</option>
<option value="Tokelau">Tokelau</option>
<option value="Tonga">Tonga</option>
<option value="Trinidad en Tobago">Trinidad en Tobago</option>
<option value="Tsjaad">Tsjaad</option>
<option value="Tsjechië">Tsjechië</option>
<option value="Tunesië">Tunesië</option>
<option value="Turkije">Turkije</option>
<option value="Turkmenistan">Turkmenistan</option>
<option value="Turks- en Caicoseilanden">Turks- en Caicoseilanden</option>
<option value="Tuvalu">Tuvalu</option>
<option value="Uruguay">Uruguay</option>
<option value="Vanuatu">Vanuatu</option>
<option value="Vaticaanstad">Vaticaanstad</option>
<option value="Venezuela">Venezuela</option>
<option value="Verenigd Koninkrijk">Verenigd Koninkrijk</option>
<option value="Verenigde Arabische Emiraten">Verenigde Arabische Emiraten</option>
<option value="Verenigde Staten">Verenigde Staten</option>
<option value="Vietnam">Vietnam</option>
<option value="Wallis en Futuna">Wallis en Futuna</option>
<option value="Westelijke Sahara">Westelijke Sahara</option>
<option value="Wit-Rusland">Wit-Rusland</option>
<option value="Zambia">Zambia</option>
<option value="Zimbabwe">Zimbabwe</option>
<option value="Zuid-Afrika">Zuid-Afrika</option>
<option value="Zuid-Georgië en Zuidelijke Sandwicheilanden">Zuid-Georgië en Zuidelijke Sandwicheilanden</option>
<option value="Zuid-Korea">Zuid-Korea</option>
<option value="Zweden">Zweden</option>
<option value="Zwitserland">Zwitserland</option>
      </select>
    </div>

    <div class="form-row">
      <div class="form-label"><label for="Phone"><?php echo __('Telefoon'); ?> *</label></div>
      <input type="text" name="Phone" id="Phone">
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="MobilePhone"><?php echo __('Telefoon mobiel'); ?> *</label></div>
      <input type="text" name="MobilePhone" id="MobilePhone">
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="PhoneExtra"><?php echo __('Telefoon extra'); ?></label></div>
      <input type="text" name="PhoneExtra" id="PhoneExtra">
    </div>
    
    <h2><?php echo __('Gegevens van het kind/ de gast'); ?></h2>
    <div class="form-row">
      <div class="form-label"><label for="PersonaFirstName"><?php echo __('Voornaam'); ?> *</label></div>
      <input type="text" name="PersonaFirstName" id="PersonaFirstName">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="PersonaInsertions"><?php echo __('Tussenvoegsels'); ?></label></div>
      <input type="text" name="PersonaInsertions" id="PersonaInsertions">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="PersonaLastName"><?php echo __('Achternaam'); ?> *</label></div>
      <input type="text" name="PersonaLastName" id="PersonaLastName">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="Gender"><?php echo __('Geslacht'); ?></label></div>
      <select name="Gender" id="Gender">
        <option value="m"><?php echo __('Man'); ?></option>
        <option value="f"><?php echo __('Vrouw'); ?></option>
      </select>
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="Dob"><?php echo __('Geboortedatum'); ?> *</label></div>
      <input type="text"<?php if(isset($_SESSION['booking']['dob'])) echo ' value="'.$_SESSION['booking']['dob'].'"'; ?> name="Dob" id="Dob" style="width:110px !important;"> ( dd-mm-jjjj )
    </div>
    
    <h2><?php echo __('Opmerkingen'); ?></h2>
    <p><?php echo __('Vul hier bijv. in eventuele allergie-informatie, medische beperkingen, of andere zaken waarvan u het belangrijk acht dat wij hiervan op de hoogte zijn.'); ?></p>

    <div class="form-row">
      <textarea name="Remarks" id="Remarks" cols="40" rows=""></textarea>
    </div>
    
    
    <h2><?php echo __('Inloggegevens'); ?></h2>

    <div class="form-row">
      <div class="form-label"><label for="Username"><?php echo __('Gebruikersnaam'); ?> *</label></div>
      <input type="text" name="Username" id="Username">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="Password"><?php echo __('Wachtwoord'); ?> *</label></div>
      <input type="password" name="Password" id="Password">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="Password2"><?php echo __('Herhaal wachtwoord'); ?> *</label></div>
      <input type="password" name="Password2" id="Password2">
      <div style="clear:both;"></div>
    </div>

    <div class="form-row">
      <input type="checkbox" name="Legal" id="Legal" class="checkbox"> <label for="Legal"><?php echo __('Ik ga akkoord met de'); ?> <a href="http://www.dekaag.nl/zeilschool/voor-ouders/algemene-voorwaarden/" target="_blank"><?php echo __('algemene voorwaarden'); ?></a> *</label>
    </div>

    <div class="form-button">
      <button style="float:left;" type="button" onclick="widget.startConsumerData(widget.options.bookingOptions);"><?php echo __('Terug'); ?></button>
      <button type="button" onclick="widget.handleRegister();"><?php echo __('Verzenden'); ?></button>
    </div>
  </fieldset>
</form>