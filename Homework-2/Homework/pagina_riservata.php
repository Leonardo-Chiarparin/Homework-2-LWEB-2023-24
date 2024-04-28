<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<?php
	require_once("./session_control.php");
	require_once("./connection.php");
	
	// CONTROLLO PER VERIFICARE SE SI È STATI REINDIRIZZATI ALLA PAGINA A SEGUITO DELLA TERMINAZIONE DI UNA CERTA OPERAZIONE (AVVENUTA CON SUCCESSO)
	if(isset($_SESSION["modifica_Effettuata"])){
		unset($_SESSION["modifica_Effettuata"]);
		
		echo "<div class='confirm_message'>\n
			  <div class='container_message'>\n
			  <div class='container_img'>\n
			  <img src=\"Immagini/check-solid.svg\" alt='Immagine non Disponibile...'>\n
			  </div>\n
			  <div class='message'>\n
			  <p class='con'>OTTIMO!</p>\n
			  <p>OPERAZIONE EFFETTUATA CON SUCCESSO!</p>\n
			  </div>\n
			  </div>\n
			  </div>\n";
		
	}
	
	// SE NON SI HANNO PRENOTAZIONI ATTIVE ALL'INTERNO DELLA BASE DI DATI E IL GENERICO UTENTE CERCASSE DI ACCEDERE ALLA PAGINA DEDICATA ALLA LORO GESTIONE, SARÀ NECESSARIO STAMPARE UN MESSAGGIO D'ERRORE
	if(isset($_SESSION["nessuna_Prenotazione"])){
		unset($_SESSION["nessuna_Prenotazione"]);
		
		echo "<div class='error_message'>\n
			  <div class='container_message'>\n
			  <div class='container_img'>\n
			  <img src=\"Immagini/exclamation-solid.svg\" alt='Immagine non Disponibile...'>\n
			  </div>\n
			  <div class='message'>\n
			  <p class='err'>ERRORE!</p>\n
			  <p>NON &Egrave; PRESENTE ALCUNA PRENOTAZIONE...</p>\n
			  </div>\n
			  </div>\n
			  </div>\n";
		
	}
	
	// SE SI TRATTA DI UN CLIENTE, SI PROCEDE CON L'OTTENIMENTO DELLE INFORMAZIONI RELATIVE ALLE PREFERENZE DI QUEST'ULTIMO. TALI ASPETTI TORNERANNO UTILI SIA PER LA GESTIONE DELLE FUTURE PRENOTAZIONI CHE PER LA DEFINIZIONE DELLA RELAZIONE CHE IL SOGGETTO D'INTERESSE HA TENUTO NEI CONFRONTI DEL CAMPO SPORTIVO
	if($_SESSION["tipo_Utente"]=="C"){
		// 1) IL CAMPO DA GIOCO SCELTO IL MAGGIOR NUMERO DI VOLTE (SI CONSIDERERÀ SOLTANTO IL PRIMO ELEMENTO UTILE)
		$sql="SELECT C.Nome AS Campo FROM Campi C, (SELECT ID_Campo, COUNT(*) AS Num_Volte FROM Prenotazioni WHERE ID_Cliente=".$_SESSION["id_Utente"]." GROUP BY ID_Campo ORDER BY Num_Volte DESC LIMIT 1) AS Conteggi WHERE C.ID=Conteggi.ID_Campo";
		$result=mysqli_query($conn, $sql);
		
		while($row=mysqli_fetch_array($result)){
			$campo_preferito=$row["Campo"];
		}
		
		// VERIFICA DEL CONTENUTO DELL'ELEMENTO APPENA INDIVIDUATO
		if(empty($campo_preferito))
			$campo_preferito="Nessuno";
		
		setcookie("Campo", $campo_preferito);
		
		// 2) LA DISCIPLINA SCELTA IL MAGGIOR NUMERO DI VOLTE (SI CONSIDERERÀ SOLTANTO IL PRIMO ELEMENTO UTILE)
		$sql="SELECT Disciplina FROM (SELECT Disciplina, COUNT(*) AS Num_Volte FROM Prenotazioni P, Campi C WHERE P.ID_Cliente=".$_SESSION["id_Utente"]." AND C.ID=P.ID_Campo GROUP BY C.Disciplina ORDER BY Num_Volte DESC LIMIT 1) AS Conteggi";
		$result=mysqli_query($conn, $sql);
		
		while($row=mysqli_fetch_array($result)){
			$disciplina_preferita=$row["Disciplina"];
		}
		
		// VERIFICA DEL CONTENUTO DELL'ELEMENTO APPENA INDIVIDUATO
		if(empty($disciplina_preferita))
			$disciplina_preferita="Nessuna";
		
		setcookie("Disciplina", $disciplina_preferita);
		
		// 3) LA FASCIA ORARIA SCELTA IL MAGGIOR NUMERO DI VOLTE (SI CONSIDERERÀ SOLTANTO IL PRIMO ELEMENTO UTILE)
		$sql="SELECT TIME_FORMAT(Ora_Inizio, '%H:%m') AS Ora_Inizio, TIME_FORMAT(Ora_Fine, '%H:%m') AS Ora_Fine FROM (SELECT Ora_Inizio, Ora_Fine, COUNT(*) AS Num_Volte FROM Prenotazioni P, Fasce_Orarie F WHERE P.ID_Cliente=".$_SESSION["id_Utente"]." AND F.ID=P.ID_Fascia_Oraria GROUP BY F.ID ORDER BY Num_Volte DESC LIMIT 1) AS Conteggi";
		$result=mysqli_query($conn, $sql);
		
		while($row=mysqli_fetch_array($result)){
			$orario_preferito=$row["Ora_Inizio"]."-".$row["Ora_Fine"];
		}
		
		// VERIFICA DEL CONTENUTO DELL'ELEMENTO APPENA INDIVIDUATO
		if(empty($orario_preferito))
			$orario_preferito="Nessuna";
		
		setcookie("Fascia", $orario_preferito);
	
	}
	
	// INTERROGAZIONE ALLA BASE DI DATI AL FINE DI CREARE DINAMICAMENTE LA PAGINA IN FUNZIONE DELL'UTENTE CHE HA EFFETTUATO L'ACCESSO 
	$sql="SELECT Nome, Cognome FROM Utenti WHERE ID=".$_SESSION["id_Utente"]; 
	$result=mysqli_query($conn, $sql);
	
	// OTTENIMENTO DELLE INFORMAZIONI RICHIESTE
	while($row=mysqli_fetch_array($result)){
		$nome=$row["Nome"];
		$cognome=$row["Cognome"];
	}
	
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>CSS: Campo Sportivo dei Sogni</title>
	<link rel="icon" href="Immagini/Logo.png" />
	<link rel="stylesheet" href="CSS/style_pagina_riservata.css" type="text/css" />
</head>
<body>
	<div class="barra_navigazione">
		<div class="container_logo">
			<img class="logo" src="Immagini/Barra.png" alt="Logo non Disponibile..." />
		</div>
		<div class="container_menu">
			<div class="menu">
				<?php
					// SE SI TRATTA DI UN CLIENTE, BISOGNA RIPORTARE LA VOCE ASSOCIATA ALLA VISUALIZZAZIONE DEL PROPRIO PROFILO
					if($_SESSION["tipo_Utente"]=="C"){
						echo "<span class=\"voce_menu\"> \n
							  <a href=\"account.php\" title=\"Account\">Account</a> \n
							  </span> \n";
					}
				?>
				<span class="voce_menu">
					<a href="login.php" title="Pagina di Login">Esci</a>
				</span>
			</div>
		</div>
	</div>
	<div class="container_corpo">
		<div class="container_principale">
			<p class="spazio_link"></p>
		
			<h1 class="saluti">Salve, <?php echo $nome." ".$cognome."!"; ?></h1>
			
			<div class="container_operazioni">
				<div class="operazione">
					<div class="anteprima">
						<div class="immagine" style="background-image: url('Immagini/Background_Prenotazione-Registrazione.jpg');">"></div>
					</div>
					<div class="paragrafo">
						<?php
							if($_SESSION["tipo_Utente"]=="C"){
								echo "<h2>Prenota il Campo!</h2> \n
									  <p> \n
									  Inserisci i dettagli della prenotazione, selezionando il <strong>campo</strong>, il <strong>giorno</strong>, la <strong>fascia oraria</strong> e soprattutto lo <strong>sport</strong> che preferisci tra quelli disponibili. Non avere pensieri prima del grande incontro, in quanto, stando alle nostre politiche, puoi benissimo pagare di persona dopo aver giocato! Inoltre, giunti sul posto, lo staff sar&agrave; ben lieto di aiutarti. \n 
									  </p> \n
									  <form action=\"sport_selection.php\" method=\"post\"> \n
									  <p><button type=\"submit\" class=\"dettagli\">Prenota!</button></p> \n
									  </form> \n";
							}
							else {
								echo "<h2>Registra il Pagamento!</h2> \n
									  <p> \n
									  Inserisci i dettagli del pagamento, selezionando la <strong>prenotazione</strong> tra quelle non ancora saldate. \n 
									  </p> \n
									  <form action=\"registra_pagamento.php\" method=\"post\"> \n
									  <p><button type=\"submit\" class=\"dettagli\">Registra!</button></p> \n
									  </form> \n";
							}
						?>
					</div>
				</div>
				<div class="operazione">
					<div class="anteprima">
						<div class="immagine" style="background-image: url('Immagini/Background_Modifiche.jpg');"></div>
					</div>
					<div class="paragrafo">
						<h2>Modifica le Richieste!</h2>
						<?php
							if($_SESSION["tipo_Utente"]=="C"){
								echo "<p> \n
									  <strong>Visualizza</strong> le prenotazioni effettuate, apportando, in base alla disponibilit&agrave; del momento, alcune <strong>modifiche</strong> relative al loro contenuto. Inoltre, qualora non ne abbiate pi&ugrave; bisogno, sentitevi liberi di <strong>disdire</strong> la vostre richieste, non &egrave; prevista alcuna penale. Vi chiediamo solo di agire per tempo, in quanto altri potrebbero desiderare di giocare! \n   
									  </p> \n";
							}
							else {
								echo "<p> \n
									  <strong>Visualizza</strong> le prenotazioni effettuate dai clienti, apportando alcune <strong>modifiche</strong> al loro contenuto. \n   
									  </p> \n";
							}
							
						?>
						<form action="gestione_prenotazioni.php" method="post">
							<p><button type="submit" class="dettagli">Modifica!</button></p>
						</form>
					</div>
				</div>
				<div class="operazione">
					<div class="anteprima">
						<div class="immagine" style="background-image: url('Immagini/Background_Riepilogo.jpg');"></div>
					</div>
					<div class="paragrafo">
						<h2>Visualizza lo Storico!</h2>
						<?php
							if($_SESSION["tipo_Utente"]=="C"){
								echo "<p> \n
									  <strong>Consulta</strong> l'elenco delle richieste fatte nel corso del tempo! Oltre che da un punto di vista <strong>nostalgico</strong>, potrebbero tornare utili al fine di determinare i vincitori dei premi che siamo soliti donare ai clienti pi&ugrave; fedeli ad ogni stagione! Non perderti l'occasione di entrare in possesso di divise o accessori autografati direttamente dai tuoi idoli! \n 
									  </p> \n";
							}
							else {
								echo "<p> \n
									  <strong>Consulta</strong> l'elenco delle richieste fatte dai clienti per determinare gli eventuali elementi di interesse. \n 
									  </p> \n";
							}
						?>
						<form action="storico_prenotazioni.php" method="post">
							<p><button type="submit" class="dettagli">Visualizza!</button></p>
						</form>
					</div>
				</div>
			</div>
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