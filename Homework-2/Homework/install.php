<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>CSS: Campo Sportivo dei Sogni</title>
	<link rel="icon" href="Immagini/Logo.png" />
	<link rel="stylesheet" href="CSS/style_pagina_riservata.css" type="text/css" />
</head>
<body>
	<?php
		require_once("./dbms_connection.php");
		
		mysqli_query($conn,"DROP DATABASE IF EXISTS cantile_chiarparin_homework_2");
		
		// CREAZIONE DEL DATABASE 
		mysqli_query($conn,"CREATE DATABASE cantile_chiarparin_homework_2");
		mysqli_query($conn,"USE cantile_chiarparin_homework_2");
		
		
		// CREAZIONE DELLE TABELLE E SUCCESSIVO POPOLAMENTO DELLE STESSE
		mysqli_query($conn, "DROP TABLE IF EXISTS Campi");
		$sql="CREATE TABLE Campi (ID int NOT NULL AUTO_INCREMENT, Nome varchar(35) NOT NULL, Tariffa decimal(4,2) NOT NULL, Disciplina varchar(10) NOT NULL, PRIMARY KEY (ID), UNIQUE (Nome) )";
		mysqli_query($conn,$sql);
		
		$sql="INSERT INTO Campi VALUES (1,'Olimpico',5.00,'Calcio a 5'),(3,'Barbera',5.00,'Calcio a 5'),(4,'San Nicola',5.00,'Calcio a 5'),(5,'Mapei',5.00,'Calcio a 5'),(6,'San Siro',6.00,'Calcio a 6'),(7,'Bernabeu',6.00,'Calcio a 6'),(8,'Camp Nou',8.00,'Calcio a 8'),(9,'Maradona',8.00,'Calcio a 8'),(10,'United Center',6.50,'Basket'),(11,'American Airlines Center',6.50,'Basket'),(12,'Arthur Ashe Stadium',8.50,'Tennis'),(13,'Beogradska Arena',8.50,'Tennis'),(14,'Indian Wells Tennis Garden',8.50,'Tennis'),(15,'Inalpi Arena',8.50,'Tennis'),(16,'Court Philippe Chatrier',8.50,'Tennis'),(17,'Rod Laver Arena',8.50,'Tennis'),(18,'Centre Court',8.50,'Tennis'),(19,'Connecticut Tennis Center',8.50,'Tennis')";
		mysqli_query($conn,$sql);
		
		mysqli_query($conn, "DROP TABLE IF EXISTS Fasce_Orarie");
		$sql="CREATE TABLE Fasce_Orarie (ID int NOT NULL AUTO_INCREMENT, Ora_Inizio time NOT NULL, Ora_Fine time NOT NULL, PRIMARY KEY (ID), UNIQUE(Ora_Inizio, Ora_Fine))";
		mysqli_query($conn,$sql);
		
		$sql="INSERT INTO Fasce_Orarie VALUES (1,'10:00:00','11:00:00'),(2,'11:00:00','12:00:00'),(3,'12:00:00','13:00:00'),(4,'13:00:00','14:00:00'),(5,'14:00:00','15:00:00'),(7,'15:00:00','16:00:00'),(8,'16:00:00','17:00:00'),(9,'17:00:00','18:00:00'),(10,'18:00:00','19:00:00'),(11,'19:00:00','20:00:00'),(12,'20:00:00','21:00:00'),(13,'21:00:00','22:00:00')";
		mysqli_query($conn,$sql);
		
		mysqli_query($conn, "DROP TABLE IF EXISTS Utenti");
		$sql="CREATE TABLE Utenti (ID int NOT NULL AUTO_INCREMENT, CF char(16) NOT NULL, Nome varchar(30) NOT NULL, Cognome varchar(35) NOT NULL, Num_Telefono varchar(10) NOT NULL, Email varchar(35) NOT NULL, Password varchar(32) NOT NULL, Tipo_Utente char(1) NOT NULL, PRIMARY KEY (ID), UNIQUE (CF), UNIQUE (Email), UNIQUE (Password))";
		mysqli_query($conn,$sql);
		
		$sql="INSERT INTO Utenti VALUES (1,'TTLSNT80D01D810O','Sante','Attalle','3497654234','attallesante@gmail.com','aae7fd469c43e20d2c6c067fb70c73bd','C'),(2,'NPCRSO72E43E472R','Rosa','Napucci','3279864356','rosanapucci@libero.it','3ad2724a97dfaf59adec5ff0168dad11','C'),(3,'FLLLCU75P46G698B','Lucia','Folla','3389010860','lucia.folla@css.it','a44ff5ed8710d0a20994a95fcaf1add6','D'),(4,'CLSMTT80L12G865Q','Matteo','Colussi','3358890642','matteo.colussi@css.it','e6ce31dd80ca7ef631c4e81492de2147','D')";
		mysqli_query($conn,$sql);
		
		mysqli_query($conn, "DROP TABLE IF EXISTS Prenotazioni");
		$sql="CREATE TABLE Prenotazioni (ID int NOT NULL AUTO_INCREMENT, Data date NOT NULL, Totale decimal(8,2) NOT NULL, Pagamento char(1) NOT NULL, ID_Cliente int NOT NULL, ID_Campo int NOT NULL, ID_Fascia_Oraria int NOT NULL, PRIMARY KEY (ID), UNIQUE (Data,ID_Campo,ID_Fascia_Oraria), CONSTRAINT Effettuata_Da FOREIGN KEY (ID_Cliente) REFERENCES Utenti(ID), CONSTRAINT Specificato_In FOREIGN KEY (ID_Campo) REFERENCES Campi(ID), CONSTRAINT Indicata_In FOREIGN KEY (ID_Fascia_Oraria) REFERENCES Fasce_Orarie(ID))";
		mysqli_query($conn,$sql);
		
		$sql="INSERT INTO Prenotazioni VALUES (1,'2024-04-27',65.00,'N',2,10,12);";
		mysqli_query($conn,$sql);
		
		// CONFERMA DELL'OPERAZIONE MEDIANTE MESSAGGIO POPUP
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
		
	?>
</body>
</html>