<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php
	require_once("./session_control.php");
	require_once("./connection.php");
	
	if($_SESSION["tipo_Utente"]=="C"){
		// BISOGNA RIPORTARE TUTTE LE PRENOTAZIONI AVENTI DATA D'INTERESSE SUCCESSIVA O UGUALE A QUELLA CORRENTE. INOLTRE, QUESTE POSSONO ESSERE MODIFICATE FINO AD UN'ORA DALL'INIZIO DELLA "PARTITA" 
		$sql="SELECT P.ID AS Num_Prenotazione, P.Data, C.Nome, TIME_FORMAT(F.Ora_Inizio, '%H:%m') AS Ora_Inizio, TIME_FORMAT(F.Ora_Fine, '%H:%m') AS Ora_Fine, P.Totale FROM Prenotazioni P, Campi C, Fasce_Orarie F WHERE P.ID_Cliente=".$_SESSION["id_Utente"]." AND P.ID_Campo=C.ID AND P.ID_Fascia_Oraria=F.ID AND P.Data>=CURDATE() AND F.Ora_Inizio>=SUBTIME(CURTIME(), '01:00:00')"; 
		$result=mysqli_query($conn, $sql);
	}
	else {
		// I DIPENDENTI POTRANNO VISUALIZZARE TUTTE LE PRENOTAZIONI AVENTI LE CARATTERISTICHE DI CUI SOPRA
		$sql="SELECT P.ID AS Num_Prenotazione, U.Nome AS Nome_Cliente, U.Cognome, U.Num_Telefono, P.Data, C.Nome, TIME_FORMAT(F.Ora_Inizio, '%H:%m') AS Ora_Inizio, TIME_FORMAT(F.Ora_Fine, '%H:%m') AS Ora_Fine, P.Totale FROM Utenti U, Prenotazioni P, Campi C, Fasce_Orarie F WHERE U.ID=P.ID_Cliente AND P.ID_Campo=C.ID AND P.ID_Fascia_Oraria=F.ID AND P.Data>=CURDATE() AND F.Ora_Inizio>=SUBTIME(CURTIME(), '01:00:00')"; 
		$result=mysqli_query($conn, $sql);
	}
	
	// SE NON SI HANNO PRENOTAZIONI IN CORSO
	if(mysqli_num_rows($result)==0){
		// VARIABILE UTILE PER LA STAMPA DEL RELATIVO MESSAGGIO D'ERRORE
		$_SESSION["nessuna_Prenotazione"]=true;
		header("Location: pagina_riservata.php");
	}
	
	// VERIFICA INERENTE ALLE SCELTE EFFETTUATE
	if(isset($_GET["confirm"])){
		if(isset($_GET["prenotazione"])){
			// SI PROCEDE CON LA CANCELLAZIONE DELLA PRENOTAZIONE
			$sql="DELETE FROM Prenotazioni WHERE ID=".$_GET["prenotazione"];
			
			if(mysqli_query($conn, $sql)){
				$_SESSION["modifica_Effettuata"]=true;
				
				header("Location: pagina_riservata.php");
			}
			else {
				echo "<div class='error_message'>\n
					  <div class='container_message'>\n
					  <div class='container_img'>\n
					  <img src=\"Immagini/exclamation-solid.svg\" alt='Immagine non Disponibile...'>\n
					  </div>\n
					  <div class='message'>\n
					  <p class='err'>ERRORE!</p>\n
					  <p>CANCELLAZIONE GI&Agrave; EFFETTUATA...</p>\n
					  </div>\n
					  </div>\n
					  </div>\n";
			}
			
		}
		else {
			echo "<div class='error_message'>\n
				  <div class='container_message'>\n
				  <div class='container_img'>\n
				  <img src=\"Immagini/exclamation-solid.svg\" alt='Immagine non Disponibile...'>\n
				  </div>\n
				  <div class='message'>\n
				  <p class='err'>ERRORE!</p>\n
				  <p>NESSUNA PRENOTAZIONE SELEZIONATA...</p>\n
				  </div>\n
				  </div>\n
				  </div>\n";
		}
	}
	
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>CSS: Campo Sportivo dei Sogni</title>
	<link rel="icon" href="Immagini/Logo.png" />
	<link rel="stylesheet" href="CSS/style_form.css" type="text/css" />
</head>
<body>
	<?php
		require_once("./menu_riservato.php");
	?>
	<div class="container_corpo">
		<div class="container_principale">
			<p class="spazio_link"></p>
		
			<h1 class="saluti">Gestione delle Prenotazioni!</h1>
			
			<!--NELLE COMPONENTI DEDICATE AL CONTENIMENTO DELL'INPUT FORNITO DALL'UTENTE, SI È DECISO DI PRESERVARE QUANTO SPECIFICATO ANCHE IN PRESENZA DI EVENTUALI ERRORI-->
			<form class="container_form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
				<div class="form">
					<div class="intestazione">
						<h2>
							Spuntare la voce di interesse e confermare per disdire il relativo impegno
						</h2>
					</div>
					<div class="container_elenco_campi">
						<div class="intestazione_elenco_campi">
							<h3>Dettagli delle Attivit&agrave; (in programma)</h3>
						</div>
						<div class="corpo_elenco_campi">
							<div class="container_sezione">
								<div class="titolo_sezione"><p>Profilo Gestionale</p></div>
							</div>
							<div class="campo">
								<div class="contenuto" style="flex-direction: column;" >
									<div class="item" style="flex-direction:row; border:none;">
										<table>
											<thead>
												<tr>
													<?php
														if($_SESSION["tipo_Utente"]=="C")
															echo "<th class=\"td_item\">N. Prenotazione</th> \n";
														else {
															echo "<th class=\"td_item\">Cliente</th> \n";
															echo "<th class=\"td_item\">Recapito Telefonico</th> \n";
														}
														
														echo "<th class=\"td_item\">Data (anno-mm-gg)</th> \n";
														echo "<th class=\"td_item\">Campo</th> \n";
														echo "<th class=\"td_item\">Fascia Oraria</th> \n";
														echo "<th class=\"td_item\">Totale (&euro;)</th> \n";
														echo "<th class=\"td_box\">Azione</th> \n";
														
													?>
												</tr>
											</thead>
											<tbody>
												<?php 
													while ($row=mysqli_fetch_array($result)) {
														echo "<tr> \n";
														if($_SESSION["tipo_Utente"]=="C")
															echo "<td class=\"td_item\">".$row["Num_Prenotazione"]."</td> \n";
														else {
															echo "<td class=\"td_item\">".$row["Nome_Cliente"]." ".$row["Cognome"]."</td> \n";
															echo "<td class=\"td_item\">".$row["Num_Telefono"]."</td> \n";
														}
														
														echo "<td class=\"td_item\">".$row["Data"]."</td> \n";
														echo "<td class=\"td_item\">".$row["Nome"]."</td> \n";
														echo "<td class=\"td_item\">".$row["Ora_Inizio"]."-".$row["Ora_Fine"]."</td> \n";
														echo "<td class=\"td_item\">".$row["Totale"]."</td> \n";
														echo "<td class=\"td_box\"><input type=\"radio\" value='".$row["Num_Prenotazione"]."' name=\"prenotazione\"></td> \n";
														echo "<tr> \n";
													}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="container_button">
								<button type="submit" name="confirm" value="confirm" class="confirm">Conferma!</button>
							</div>  
						</div>
					</div>
				</div>
			</form>
			<div class="blank_space"></div>
		</div>
		<div class="footer">
			<p>
				Ettore Cantile e Leonardo Chiarparin, Linguaggi per il Web  a.a. 2023-2024
			</p>
		</div>
	</div>
</body>
</html>