<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php
	require_once("./sport_cookie_control.php");
	require_once("./session_control.php");
	
	// NELL'EVENTUALITÀ IN CUI CI SIA UN TENTATIVO DI ACCESSO DA PARTE DI UN DIPENDENTE, BISOGNA REINDERIZZARLO ALLA PAGINA INIZIALE DELL'AREA RISERVATA 
	if($_SESSION["tipo_Utente"]!="C")
		header ("Location: pagina_riservata.php");
	
	require_once("./connection.php");
	
	// OTTENIMENTO DEI CAMPI DA GIOCO INERENTI ALLA DISCIPLINA SELEZIONATA IN PRECEDENZA
	$sql_campi="SELECT Nome, Tariffa FROM Campi WHERE Disciplina='".$_COOKIE["Disciplina_Scelta"]."'";
	$result_campi=mysqli_query($conn, $sql_campi);
	
	// OTTENIMENTO DELLE VARIE FASCE ORARIE
	$sql_fasce="SELECT TIME_FORMAT(Ora_Inizio, '%H:%m') AS Ora_Inizio, TIME_FORMAT(Ora_Fine, '%H:%m') AS Ora_Fine FROM Fasce_Orarie";
	$result_fasce=mysqli_query($conn, $sql_fasce);
	
	// UNA VOLTA PREMUTO IL PULSANTE DI CONFERMA, DOVRANNO ESSERE EFFETTUATI DEI CONTROLLI IN MERITO ALLA CORRETTEZZA DEI VALORI INSERITI
	if(isset($_GET["confirm"])){
		if(isset($_GET["campo"]) && isset($_GET["fascia"])){
			// VERIFICA DEL FORMATO INERENTE ALLA DATA INSERITA (ANNO-MESE-GIORNO) 
			if(preg_match("/(\d{4,4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]))/",$_GET["date"], $matches))
			{
				// SE QUANTO INSERITO NON RISPETTA LA GRAMMATICA INDICATA DALL'ESPRESSIONE REGOLARE, BISOGNA STAMPARE UN MESSAGGIO COSÌ DA NOTIFICARLO ALL'UTENTE
				if($matches[0]!=$_GET["date"])
				{
					echo "<div class='error_message'>\n
					      <div class='container_message'>\n
						  <div class='container_img'>\n
						  <img src=\"Immagini/exclamation-solid.svg\" alt='Immagine non Disponibile...'>\n
					      </div>\n
					      <div class='message'>\n
					      <p class='err'>ERRORE!</p>\n
					      <p>LA DATA NON RISPETTA IL FORMATO INDICATO...</p>\n
					      </div>\n
					      </div>\n
					      </div>\n";
				}
				else {
					// COSTRUZIONE DEL TIMESTAMP SPECIFICATO MEDIANTE CONCATENAZIONE DELLA DATA E DEL VALORE TRONCATO (SOLO ORA INIZIALE) DELLA FASCIA ORARIA SELEZIONATA
					$timestamp_indicato=$_GET["date"]." ".substr($_GET["fascia"],0,8);
					
					// OTTENIMENTO DELL'ISTANTE DI TEMPO ATTUALE (DIFFERENZA DI SECONDI DAL GIORNO 01/01/1990 00:00:00)
					$timestamp_attuale=time();
					
					// CONTROLLO ,MEDIANTE CONVERSIONE IN TEMPO E CONFRONTO CON LA PRECEDENTE, IN MERITO ALLA VALIDITÀ TEMPORALE DELLA DATA E DELL'ORARIO FORNITI
					if(strtotime($timestamp_indicato)>$timestamp_attuale) {
						// PROCEDIAMO CON IL TENTATIVO DI INSERIMENTO DELLA PRENOTAZIONE ALL'INTERNO DEL DATABASE
						
						// OTTENIMENTO DELL'ORA INIZIALE E FINALE
						$ora_inizio=substr($_GET["fascia"],0,8);
						$ora_fine=substr($_GET["fascia"],-9,-1);
						
						// PRELEVIAMO, MEDIANTE LA "LETTURA" A RITROSO DEI CARATTERI, LA TARIFFA DALL'ELEMENTO RELATIVO AL NOME DEL CAMPO
						$tariffa=substr($_GET["campo"], -5, -1);
						$campo=substr($_GET["campo"], 0, -6);
						
						// CALCOLO DEL TOTALE A SECONDA DELLA DISCIPLINA
						if($_COOKIE["Disciplina_Scelta"]=="Calcio a 5")
							$totale=2*5*$tariffa;
						
						if($_COOKIE["Disciplina_Scelta"]=="Calcio a 6")
							$totale=2*6*$tariffa;
						
						if($_COOKIE["Disciplina_Scelta"]=="Calcio a 8")
							$totale=2*8*$tariffa;
						
						if($_COOKIE["Disciplina_Scelta"]=="Basket")
							$totale=2*5*$tariffa;
						
						if($_COOKIE["Disciplina_Scelta"]=="Tennis")
							$totale=2*$tariffa;
						
						try {
							// N.B. IL CAMPO PAGAMENTO VIENE INIZIALIZZATO A NO POICHÈ VERRÀ AGGIORNATO UNA VOLTA RICEVUTO L'IMPORTO
							$sql="INSERT INTO Prenotazioni VALUES (NULL,'".$_GET["date"]."',".$totale.",'N',".$_SESSION["id_Utente"].",(SELECT ID FROM Campi WHERE Nome='".$campo."'), (SELECT ID FROM Fasce_Orarie WHERE Ora_Inizio='".$ora_inizio."' AND Ora_Fine='".$ora_fine."'))";
							
							if(mysqli_query($conn,$sql)){
								// VARIABILE UTILE AL FINE DI STAMPARE UN MESSAGGIO DI CONFERMA
								$_SESSION["modifica_Effettuata"]=true;
								
								header("Location: pagina_riservata.php");
							}
							else {
								throw new mysqli_sql_exception;
							}
						}
						catch(mysqli_sql_exception $e){
							echo "<div class='error_message'>\n
									  <div class='container_message'>\n
									  <div class='container_img'>\n
									  <img src=\"Immagini/exclamation-solid.svg\" alt='Immagine non Disponibile...'>\n
									  </div>\n
									  <div class='message'>\n
									  <p class='err'>ERRORE!</p>\n
									  <p>DATA INESISTENTE O CAMPO GI&Agrave; PRENOTATO AL GIORNO E ALL'ORARIO INDICATO...</p>\n
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
							  <p>LA DATA E L'ORARIO INDICATI NON RISULTANO VALIDI...</p>\n
							  </div>\n
							  </div>\n
							  </div>\n";
					}
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
					  <p>LA DATA NON RISPETTA IL FORMATO INDICATO...</p>\n
					  </div>\n
					  </div>\n
					  </div>\n";
			}
		}
		else {
			// STAMPA DEL RELATIVO MESSAGGIO D'ERRORE
			echo "<div class='error_message'>\n
				   <div class='container_message'>\n
				   <div class='container_img'>\n
                   <img src=\"Immagini/exclamation-solid.svg\" alt='Immagine non Disponibile...'>\n
				   </div>\n
                   <div class='message'>\n
                   <p class='err'>ERRORE!</p>\n
                   <p>BISOGNA COMPILARE TUTTI I CAMPI...</p>\n
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
	<div class="barra_navigazione">
		<div class="container_logo">
			<img class="logo" src="Immagini/Barra.png" alt="Logo non Disponibile..." />
		</div>
		<div class="container_menu">
			<div class="menu">
				<span class="voce_menu">
					<a href="sport_selection.php" title="Selezione della Disciplina">Annulla</a>
				</span>
				
				<span class="voce_menu">
					<a href="login.php" title="Esci">Esci</a>
				</span>
			</div>
		</div>
	</div>
	<div class="container_corpo">
		<div class="container_principale">
			<p class="spazio_link"></p>
		
			<h1 class="saluti">Inserimento della Prenotazione!</h1>
			
			<form class="container_form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
				<div class="form">
					<div class="intestazione">
						<h2>
							Compilare i seguenti campi con le informazioni richieste
						</h2>
					</div>
					<div class="container_elenco_campi">
						<div class="intestazione_elenco_campi">
							<h3>Dettagli della Prenotazione</h3>
						</div>
						<div class="corpo_elenco_campi">
							<div class="campo">
								<div class="contenuto" style="flex-direction: column;" >
									<div class="item" style="flex-direction:row; border:none;">
										<p class="nome_campo">
											Data
										</p>
										<p class="dettagli_campo">
											<input type="text" name="date" value="<?php if(isset($_GET['date'])) echo $_GET['date']; else echo '';?>"  />
										</p>	
									</div>
									<p style="font-size: 1em; color: red; width: 100%; margin-left: 1em;"><strong style="text-decoration: underline;">N.B.</strong> La data dovr&agrave; rispettare il formato anno-mese-giorno </p>
								</div>
							</div>
							<div class="campo_radio">
								<div class="contenuto">
									<div class="item">
										<div class="titolo">
											<p>Campo da Gioco</p>
										</div>
										<div class="voci">
											<?php 
												while($row=mysqli_fetch_array($result_campi)){
													echo "<div class=\"voce\"> \n
														  <p style=\"padding-right: 0.5%;\"> \n
														  <input type=\"radio\" name=\"campo\" value='".$row["Nome"]." ".$row["Tariffa"]." '";
														  
													// LA VOCE INERENTE AL CAMPO SARÀ SPUNTATA NEL CASO IN CUI CORRISPONDA ALLA PREFERENZA (PRIMA INDIVIDUATA ALL'INTERNO DELLA BASE DI DATI) DEL CLIENTE 	  
													if(isset($_COOKIE["Campo"]) && $_COOKIE["Campo"]==$row["Nome"])
														echo  " checked=\"checked\" "; 
													
													echo "/> \n
														  </p> \n 
														  <p style=\"margin-top: -0.5%;\"> \n ".$row["Nome"]." (".$row["Tariffa"]."&euro; orari) \n
														  </p> \n
														  </div> \n ";
												}
											?>
										</div>
									</div>
								</div>
							</div>
							<div class="campo_radio">
								<div class="contenuto">
									<div class="item">
										<div class="titolo">
											<p>Fascia Oraria</p>
										</div>
										<div class="voci">
											<?php 
												// NELL'INTERROGAZIONE EFFETTUATA ALLA BASE DI DATI, SI È RICHIESTO UN FORMATO CHE PRIVASSE L'ORARIO DEI SECONDI. PROPRIO PER QUESTO, NEL VALORE REALE DI CIASCUN CAMPO È STATO NECESSARIO INSERIRE NUOVAMENTE GLI ELEMENTI MANCANTI
												while($row=mysqli_fetch_array($result_fasce)){
													echo "<div class=\"voce\"> \n 
														  <p style=\"padding-right: 0.5%;\"> \n
														  <input type=\"radio\" name=\"fascia\" value='".$row["Ora_Inizio"].":00-".$row["Ora_Fine"].":00 '";
													
													// LA VOCE INERENTE ALL'ORARIO SARÀ SPUNTATA NEL CASO IN CUI CORRISPONDA ALLA PREFERENZA (PRIMA INDIVIDUATA ALL'INTERNO DELLA BASE DI DATI) DEL CLIENTE 	  
													if(isset($_COOKIE["Fascia"]) && $_COOKIE["Fascia"]==($row["Ora_Inizio"]."-".$row["Ora_Fine"]))
														echo  " checked=\"checked\" "; 
													
													echo "/> \n
														  </p> \n 
														  <p style=\"margin-top: -0.5%;\"> \n ".$row["Ora_Inizio"]." - ".$row["Ora_Fine"]." \n
														  </p> \n
														  </div> \n ";
												}
											?>
										</div>
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