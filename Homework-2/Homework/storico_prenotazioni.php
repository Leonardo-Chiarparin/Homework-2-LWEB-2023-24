<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php
	require_once("./session_control.php");
	require_once("./connection.php");
	
	if($_SESSION["tipo_Utente"]=="C"){
		// BISOGNA RIPORTARE TUTTE LE INFORMAZIONI INERENTI ALLE PRENOTAZIONI DEL CLIENTE D'INTERESSE  
		$sql="SELECT P.ID AS Num_Prenotazione, P.Data, C.Nome, TIME_FORMAT(F.Ora_Inizio, '%H:%i') AS Ora_Inizio, TIME_FORMAT(F.Ora_Fine, '%H:%i') AS Ora_Fine, P.Totale, P.Pagamento FROM Prenotazioni P, Campi C, Fasce_Orarie F WHERE P.ID_Cliente=".$_SESSION["id_Utente"]." AND P.ID_Campo=C.ID AND P.ID_Fascia_Oraria=F.ID"; 
		$result=mysqli_query($conn, $sql);
	}
	else {
		// I DIPENDENTI POTRANNO VISUALIZZARE TUTTE LE PRENOTAZIONI
		$sql="SELECT U.Nome AS Nome_Cliente, U.Cognome, U.Num_Telefono, P.Data, C.Nome, TIME_FORMAT(F.Ora_Inizio, '%H:%i') AS Ora_Inizio, TIME_FORMAT(F.Ora_Fine, '%H:%i') AS Ora_Fine, P.Totale, P.Pagamento FROM Utenti U, Prenotazioni P, Campi C, Fasce_Orarie F WHERE U.ID=P.ID_Cliente AND P.ID_Campo=C.ID AND P.ID_Fascia_Oraria=F.ID"; 
		$result=mysqli_query($conn, $sql);
	}
	
	// SE NON SI HANNO PRENOTAZIONI
	if(mysqli_num_rows($result)==0){
		// VARIABILE UTILE PER LA STAMPA DEL RELATIVO MESSAGGIO D'ERRORE
		$_SESSION["nessuna_Prenotazione"]=true;
		header("Location: pagina_riservata.php");
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
		
			<h1 class="saluti">Riepilogo delle Prenotazioni!</h1>
			
			<!--NELLE COMPONENTI DEDICATE AL CONTENIMENTO DELL'INPUT FORNITO DALL'UTENTE, SI È DECISO DI PRESERVARE QUANTO SPECIFICATO ANCHE IN PRESENZA DI EVENTUALI ERRORI-->
			<form class="container_form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
				<div class="form">
					<div class="intestazione">
						<h2>
							Premere una delle voci del men&ugrave; per tornare ad una delle pagine precedenti
						</h2>
					</div>
					<div class="container_elenco_campi">
						<div class="intestazione_elenco_campi">
							<h3>Dettagli dello Storico</h3>
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
														echo "<th class=\"td_item\">Saldata? (Y/N)</th> \n";
		
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
														echo "<td class=\"td_item\">".$row["Pagamento"]."</td> \n";
														echo "<tr> \n";
													}
												?>
											</tbody>
										</table>
									</div>
								</div>
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