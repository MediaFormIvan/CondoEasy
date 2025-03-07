-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 13, 2025 alle 15:26
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gpisrl`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `assicurazioni`
--

CREATE TABLE `assicurazioni` (
  `IDAssicurazione` int(10) UNSIGNED NOT NULL,
  `IDCondominio` int(10) UNSIGNED NOT NULL,
  `IDFornitore` int(10) UNSIGNED NOT NULL,
  `DataScadenza` date NOT NULL,
  `Durata` int(11) NOT NULL,
  `Polizza` varchar(255) NOT NULL,
  `Creato` datetime NOT NULL DEFAULT current_timestamp(),
  `Modificato` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `assicurazioni_documenti`
--

CREATE TABLE `assicurazioni_documenti` (
  `IDAssicurazioneDocumento` int(10) UNSIGNED NOT NULL,
  `IDAssicurazione` int(10) UNSIGNED NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `File` varchar(255) NOT NULL,
  `Creato` datetime NOT NULL DEFAULT current_timestamp(),
  `Modificato` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `banche`
--

CREATE TABLE `banche` (
  `IDBanca` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `condomini`
--

CREATE TABLE `condomini` (
  `IDCondominio` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Indirizzo` varchar(255) DEFAULT NULL,
  `Cap` varchar(10) DEFAULT NULL,
  `Citta` varchar(100) DEFAULT NULL,
  `CodiceFiscale` varchar(50) DEFAULT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `contratti`
--

CREATE TABLE `contratti` (
  `IDCondominioContratto` int(10) UNSIGNED NOT NULL,
  `IDCondominio` int(10) UNSIGNED NOT NULL,
  `IDFornitore` int(10) UNSIGNED NOT NULL,
  `IDTipoContratto` int(10) UNSIGNED NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `DataInizio` date NOT NULL,
  `DataFine` date NOT NULL,
  `Note` text DEFAULT NULL,
  `Creato` datetime NOT NULL DEFAULT current_timestamp(),
  `Modificato` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `contratti_documenti`
--

CREATE TABLE `contratti_documenti` (
  `IDContrattoDocumento` int(10) UNSIGNED NOT NULL,
  `IDContratto` int(10) UNSIGNED NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `NomeFile` varchar(255) NOT NULL,
  `Creato` datetime NOT NULL DEFAULT current_timestamp(),
  `Modificato` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `fornitori`
--

CREATE TABLE `fornitori` (
  `IDFornitore` int(11) NOT NULL,
  `IDTipoFornitore` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Indirizzo` varchar(255) DEFAULT NULL,
  `Cap` varchar(10) DEFAULT NULL,
  `Citta` varchar(100) DEFAULT NULL,
  `PartitaIva` varchar(50) DEFAULT NULL,
  `CodiceFiscale` varchar(50) DEFAULT NULL,
  `Iban` varchar(50) DEFAULT NULL,
  `Telefono` varchar(50) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `Pec` varchar(100) DEFAULT NULL,
  `Note` text DEFAULT NULL,
  `CodiceRitenuta` varchar(50) DEFAULT NULL,
  `Ritenuta` tinyint(1) DEFAULT 0,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `legale`
--

CREATE TABLE `legale` (
  `IDLegale` int(11) NOT NULL,
  `IDCondominio` int(11) NOT NULL,
  `IDFornitore` int(11) DEFAULT NULL,
  `IDStato` int(11) NOT NULL,
  `DataApertura` datetime NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `Descrizione` text DEFAULT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `legale_chat`
--

CREATE TABLE `legale_chat` (
  `IDLegaleChat` int(11) NOT NULL,
  `IDLegale` int(11) NOT NULL,
  `Testo` text NOT NULL,
  `Data` date NOT NULL,
  `Orario` time NOT NULL,
  `IDUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `legale_documenti`
--

CREATE TABLE `legale_documenti` (
  `IDLegaleDocumento` int(11) NOT NULL,
  `IDLegale` int(11) NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `File` varchar(255) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `manutenzioni`
--

CREATE TABLE `manutenzioni` (
  `IDManutenzione` int(11) NOT NULL,
  `IDCondominio` int(11) NOT NULL,
  `DataApertura` datetime NOT NULL,
  `IDFornitore` int(11) DEFAULT NULL,
  `Titolo` varchar(255) NOT NULL,
  `Descrizione` text DEFAULT NULL,
  `IDStato` int(11) NOT NULL,
  `IDUser` int(11) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `manutenzioni_chat`
--

CREATE TABLE `manutenzioni_chat` (
  `IDManutenzioneChat` int(11) NOT NULL,
  `IDManutenzione` int(11) NOT NULL,
  `Testo` text NOT NULL,
  `Data` date NOT NULL,
  `Orario` time NOT NULL,
  `IDUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `manutenzioni_documenti`
--

CREATE TABLE `manutenzioni_documenti` (
  `IDManutenzioneDocumento` int(11) NOT NULL,
  `IDManutenzione` int(11) NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `File` varchar(255) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `persone`
--

CREATE TABLE `persone` (
  `IDPersona` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Cognome` varchar(255) NOT NULL,
  `CodiceFiscale` varchar(50) DEFAULT NULL,
  `Indirizzo` varchar(255) DEFAULT NULL,
  `Cap` varchar(10) DEFAULT NULL,
  `Citta` varchar(100) DEFAULT NULL,
  `Provincia` varchar(50) DEFAULT NULL,
  `Telefono` varchar(50) DEFAULT NULL,
  `Telefono2` varchar(50) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `Pec` varchar(100) DEFAULT NULL,
  `Note` text DEFAULT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `ruoli`
--

CREATE TABLE `ruoli` (
  `IDRuolo` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `scadenze`
--

CREATE TABLE `scadenze` (
  `IDScadenza` int(10) UNSIGNED NOT NULL,
  `IDCondominio` int(10) UNSIGNED NOT NULL,
  `IDTipoScadenza` int(10) UNSIGNED NOT NULL,
  `DataScadenza` date NOT NULL,
  `Durata` int(11) NOT NULL,
  `IDFornitore` int(11) NOT NULL,
  `Note` text DEFAULT NULL,
  `Creato` datetime NOT NULL DEFAULT current_timestamp(),
  `Modificato` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `scadenze_documenti`
--

CREATE TABLE `scadenze_documenti` (
  `IDScadenzaDocumento` int(10) UNSIGNED NOT NULL,
  `IDScadenza` int(10) UNSIGNED NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `File` varchar(255) NOT NULL,
  `Creato` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `sinistri`
--

CREATE TABLE `sinistri` (
  `IDSinistro` int(11) NOT NULL,
  `IDCondominio` int(11) NOT NULL,
  `IDStato` int(11) NOT NULL,
  `DataApertura` datetime NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `Descrizione` text DEFAULT NULL,
  `Numero` varchar(50) DEFAULT NULL,
  `IDStudioPeritale` int(11) DEFAULT NULL,
  `DataChiusura` datetime DEFAULT NULL,
  `Rimborso` decimal(10,2) DEFAULT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `sinistri_chat`
--

CREATE TABLE `sinistri_chat` (
  `IDSinistroChat` int(11) NOT NULL,
  `IDSinistro` int(11) NOT NULL,
  `Testo` text NOT NULL,
  `Data` date NOT NULL,
  `Orario` time NOT NULL,
  `IDUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `sinistri_documenti`
--

CREATE TABLE `sinistri_documenti` (
  `IDSinistroDocumento` int(11) NOT NULL,
  `IDSinistro` int(11) NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `File` varchar(255) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `sinistri_foto`
--

CREATE TABLE `sinistri_foto` (
  `IDSinistroFoto` int(11) NOT NULL,
  `IDSinistro` int(11) NOT NULL,
  `File` varchar(255) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `stati`
--

CREATE TABLE `stati` (
  `IDStato` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `studi_peritali`
--

CREATE TABLE `studi_peritali` (
  `IDStudioPeritale` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Telefono` varchar(50) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `tipi_contratti`
--

CREATE TABLE `tipi_contratti` (
  `IDTipoContratto` int(10) UNSIGNED NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Creato` datetime NOT NULL DEFAULT current_timestamp(),
  `Modificato` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `tipi_fornitori`
--

CREATE TABLE `tipi_fornitori` (
  `IDTipoFornitore` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `tipi_scadenze`
--

CREATE TABLE `tipi_scadenze` (
  `IDTipoScadenza` int(10) UNSIGNED NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Durata` int(11) NOT NULL,
  `Creato` datetime NOT NULL DEFAULT current_timestamp(),
  `Modificato` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `IDUtente` int(11) NOT NULL,
  `IDRuolo` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `Mail` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Token` varchar(64) DEFAULT NULL,
  `Creato` datetime DEFAULT current_timestamp(),
  `Modificato` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Archiviato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `assicurazioni`
--
ALTER TABLE `assicurazioni`
  ADD PRIMARY KEY (`IDAssicurazione`);

--
-- Indici per le tabelle `assicurazioni_documenti`
--
ALTER TABLE `assicurazioni_documenti`
  ADD PRIMARY KEY (`IDAssicurazioneDocumento`),
  ADD KEY `fk_assicurazioni_documenti_assicurazioni` (`IDAssicurazione`);

--
-- Indici per le tabelle `banche`
--
ALTER TABLE `banche`
  ADD PRIMARY KEY (`IDBanca`);

--
-- Indici per le tabelle `condomini`
--
ALTER TABLE `condomini`
  ADD PRIMARY KEY (`IDCondominio`);

--
-- Indici per le tabelle `contratti`
--
ALTER TABLE `contratti`
  ADD PRIMARY KEY (`IDCondominioContratto`),
  ADD KEY `fk_contratti_tipo` (`IDTipoContratto`);

--
-- Indici per le tabelle `contratti_documenti`
--
ALTER TABLE `contratti_documenti`
  ADD PRIMARY KEY (`IDContrattoDocumento`),
  ADD KEY `fk_documenti_contratti` (`IDContratto`);

--
-- Indici per le tabelle `fornitori`
--
ALTER TABLE `fornitori`
  ADD PRIMARY KEY (`IDFornitore`),
  ADD KEY `IDTipoFornitore` (`IDTipoFornitore`);

--
-- Indici per le tabelle `legale`
--
ALTER TABLE `legale`
  ADD PRIMARY KEY (`IDLegale`),
  ADD KEY `fk_legale_condomini` (`IDCondominio`),
  ADD KEY `fk_legale_fornitori` (`IDFornitore`),
  ADD KEY `fk_legale_stati` (`IDStato`);

--
-- Indici per le tabelle `legale_chat`
--
ALTER TABLE `legale_chat`
  ADD PRIMARY KEY (`IDLegaleChat`),
  ADD KEY `fk_legale_chat_legale` (`IDLegale`),
  ADD KEY `fk_legale_chat_utenti` (`IDUser`);

--
-- Indici per le tabelle `legale_documenti`
--
ALTER TABLE `legale_documenti`
  ADD PRIMARY KEY (`IDLegaleDocumento`),
  ADD KEY `fk_legale_documenti_legale` (`IDLegale`);

--
-- Indici per le tabelle `manutenzioni`
--
ALTER TABLE `manutenzioni`
  ADD PRIMARY KEY (`IDManutenzione`);

--
-- Indici per le tabelle `manutenzioni_chat`
--
ALTER TABLE `manutenzioni_chat`
  ADD PRIMARY KEY (`IDManutenzioneChat`),
  ADD KEY `IDManutenzione` (`IDManutenzione`),
  ADD KEY `IDUser` (`IDUser`);

--
-- Indici per le tabelle `manutenzioni_documenti`
--
ALTER TABLE `manutenzioni_documenti`
  ADD PRIMARY KEY (`IDManutenzioneDocumento`);

--
-- Indici per le tabelle `persone`
--
ALTER TABLE `persone`
  ADD PRIMARY KEY (`IDPersona`);

--
-- Indici per le tabelle `ruoli`
--
ALTER TABLE `ruoli`
  ADD PRIMARY KEY (`IDRuolo`);

--
-- Indici per le tabelle `scadenze`
--
ALTER TABLE `scadenze`
  ADD PRIMARY KEY (`IDScadenza`),
  ADD KEY `fk_scadenze_tipiscadenze` (`IDTipoScadenza`);

--
-- Indici per le tabelle `scadenze_documenti`
--
ALTER TABLE `scadenze_documenti`
  ADD PRIMARY KEY (`IDScadenzaDocumento`),
  ADD KEY `fk_scadenze_documenti_scadenze` (`IDScadenza`);

--
-- Indici per le tabelle `sinistri`
--
ALTER TABLE `sinistri`
  ADD PRIMARY KEY (`IDSinistro`),
  ADD KEY `fk_sinistri_condomini` (`IDCondominio`),
  ADD KEY `fk_sinistri_stati` (`IDStato`),
  ADD KEY `fk_sinistri_studi_peritali` (`IDStudioPeritale`);

--
-- Indici per le tabelle `sinistri_chat`
--
ALTER TABLE `sinistri_chat`
  ADD PRIMARY KEY (`IDSinistroChat`),
  ADD KEY `fk_sinistri_chat_sinistri` (`IDSinistro`),
  ADD KEY `fk_sinistri_chat_utenti` (`IDUser`);

--
-- Indici per le tabelle `sinistri_documenti`
--
ALTER TABLE `sinistri_documenti`
  ADD PRIMARY KEY (`IDSinistroDocumento`),
  ADD KEY `fk_sinistri_documenti_sinistri` (`IDSinistro`);

--
-- Indici per le tabelle `sinistri_foto`
--
ALTER TABLE `sinistri_foto`
  ADD PRIMARY KEY (`IDSinistroFoto`),
  ADD KEY `fk_sinistri_foto_sinistri` (`IDSinistro`);

--
-- Indici per le tabelle `stati`
--
ALTER TABLE `stati`
  ADD PRIMARY KEY (`IDStato`);

--
-- Indici per le tabelle `studi_peritali`
--
ALTER TABLE `studi_peritali`
  ADD PRIMARY KEY (`IDStudioPeritale`);

--
-- Indici per le tabelle `tipi_contratti`
--
ALTER TABLE `tipi_contratti`
  ADD PRIMARY KEY (`IDTipoContratto`);

--
-- Indici per le tabelle `tipi_fornitori`
--
ALTER TABLE `tipi_fornitori`
  ADD PRIMARY KEY (`IDTipoFornitore`);

--
-- Indici per le tabelle `tipi_scadenze`
--
ALTER TABLE `tipi_scadenze`
  ADD PRIMARY KEY (`IDTipoScadenza`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`IDUtente`),
  ADD UNIQUE KEY `Mail` (`Mail`),
  ADD KEY `IDRuolo` (`IDRuolo`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `assicurazioni`
--
ALTER TABLE `assicurazioni`
  MODIFY `IDAssicurazione` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `assicurazioni_documenti`
--
ALTER TABLE `assicurazioni_documenti`
  MODIFY `IDAssicurazioneDocumento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `banche`
--
ALTER TABLE `banche`
  MODIFY `IDBanca` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `condomini`
--
ALTER TABLE `condomini`
  MODIFY `IDCondominio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `contratti`
--
ALTER TABLE `contratti`
  MODIFY `IDCondominioContratto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `contratti_documenti`
--
ALTER TABLE `contratti_documenti`
  MODIFY `IDContrattoDocumento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `fornitori`
--
ALTER TABLE `fornitori`
  MODIFY `IDFornitore` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `legale`
--
ALTER TABLE `legale`
  MODIFY `IDLegale` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `legale_chat`
--
ALTER TABLE `legale_chat`
  MODIFY `IDLegaleChat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `legale_documenti`
--
ALTER TABLE `legale_documenti`
  MODIFY `IDLegaleDocumento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `manutenzioni`
--
ALTER TABLE `manutenzioni`
  MODIFY `IDManutenzione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `manutenzioni_chat`
--
ALTER TABLE `manutenzioni_chat`
  MODIFY `IDManutenzioneChat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `manutenzioni_documenti`
--
ALTER TABLE `manutenzioni_documenti`
  MODIFY `IDManutenzioneDocumento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `persone`
--
ALTER TABLE `persone`
  MODIFY `IDPersona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `ruoli`
--
ALTER TABLE `ruoli`
  MODIFY `IDRuolo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `scadenze`
--
ALTER TABLE `scadenze`
  MODIFY `IDScadenza` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `scadenze_documenti`
--
ALTER TABLE `scadenze_documenti`
  MODIFY `IDScadenzaDocumento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `sinistri`
--
ALTER TABLE `sinistri`
  MODIFY `IDSinistro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `sinistri_chat`
--
ALTER TABLE `sinistri_chat`
  MODIFY `IDSinistroChat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `sinistri_documenti`
--
ALTER TABLE `sinistri_documenti`
  MODIFY `IDSinistroDocumento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `sinistri_foto`
--
ALTER TABLE `sinistri_foto`
  MODIFY `IDSinistroFoto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `stati`
--
ALTER TABLE `stati`
  MODIFY `IDStato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `studi_peritali`
--
ALTER TABLE `studi_peritali`
  MODIFY `IDStudioPeritale` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tipi_contratti`
--
ALTER TABLE `tipi_contratti`
  MODIFY `IDTipoContratto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tipi_fornitori`
--
ALTER TABLE `tipi_fornitori`
  MODIFY `IDTipoFornitore` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tipi_scadenze`
--
ALTER TABLE `tipi_scadenze`
  MODIFY `IDTipoScadenza` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `IDUtente` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `assicurazioni_documenti`
--
ALTER TABLE `assicurazioni_documenti`
  ADD CONSTRAINT `fk_assicurazioni_documenti_assicurazioni` FOREIGN KEY (`IDAssicurazione`) REFERENCES `assicurazioni` (`IDAssicurazione`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `contratti`
--
ALTER TABLE `contratti`
  ADD CONSTRAINT `fk_contratti_tipo` FOREIGN KEY (`IDTipoContratto`) REFERENCES `tipi_contratti` (`IDTipoContratto`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `contratti_documenti`
--
ALTER TABLE `contratti_documenti`
  ADD CONSTRAINT `fk_documenti_contratti` FOREIGN KEY (`IDContratto`) REFERENCES `contratti` (`IDCondominioContratto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `fornitori`
--
ALTER TABLE `fornitori`
  ADD CONSTRAINT `fornitori_ibfk_1` FOREIGN KEY (`IDTipoFornitore`) REFERENCES `tipi_fornitori` (`IDTipoFornitore`);

--
-- Limiti per la tabella `legale`
--
ALTER TABLE `legale`
  ADD CONSTRAINT `fk_legale_condomini` FOREIGN KEY (`IDCondominio`) REFERENCES `condomini` (`IDCondominio`),
  ADD CONSTRAINT `fk_legale_fornitori` FOREIGN KEY (`IDFornitore`) REFERENCES `fornitori` (`IDFornitore`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_legale_stati` FOREIGN KEY (`IDStato`) REFERENCES `stati` (`IDStato`);

--
-- Limiti per la tabella `legale_chat`
--
ALTER TABLE `legale_chat`
  ADD CONSTRAINT `fk_legale_chat_legale` FOREIGN KEY (`IDLegale`) REFERENCES `legale` (`IDLegale`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_legale_chat_utenti` FOREIGN KEY (`IDUser`) REFERENCES `utenti` (`IDUtente`) ON DELETE CASCADE;

--
-- Limiti per la tabella `legale_documenti`
--
ALTER TABLE `legale_documenti`
  ADD CONSTRAINT `fk_legale_documenti_legale` FOREIGN KEY (`IDLegale`) REFERENCES `legale` (`IDLegale`) ON DELETE CASCADE;

--
-- Limiti per la tabella `scadenze`
--
ALTER TABLE `scadenze`
  ADD CONSTRAINT `fk_scadenze_tipiscadenze` FOREIGN KEY (`IDTipoScadenza`) REFERENCES `tipi_scadenze` (`IDTipoScadenza`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `scadenze_documenti`
--
ALTER TABLE `scadenze_documenti`
  ADD CONSTRAINT `fk_scadenze_documenti_scadenze` FOREIGN KEY (`IDScadenza`) REFERENCES `scadenze` (`IDScadenza`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `sinistri`
--
ALTER TABLE `sinistri`
  ADD CONSTRAINT `fk_sinistri_condomini` FOREIGN KEY (`IDCondominio`) REFERENCES `condomini` (`IDCondominio`),
  ADD CONSTRAINT `fk_sinistri_stati` FOREIGN KEY (`IDStato`) REFERENCES `stati` (`IDStato`),
  ADD CONSTRAINT `fk_sinistri_studi_peritali` FOREIGN KEY (`IDStudioPeritale`) REFERENCES `studi_peritali` (`IDStudioPeritale`) ON DELETE SET NULL;

--
-- Limiti per la tabella `sinistri_chat`
--
ALTER TABLE `sinistri_chat`
  ADD CONSTRAINT `fk_sinistri_chat_sinistri` FOREIGN KEY (`IDSinistro`) REFERENCES `sinistri` (`IDSinistro`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sinistri_chat_utenti` FOREIGN KEY (`IDUser`) REFERENCES `utenti` (`IDUtente`) ON DELETE CASCADE;

--
-- Limiti per la tabella `sinistri_documenti`
--
ALTER TABLE `sinistri_documenti`
  ADD CONSTRAINT `fk_sinistri_documenti_sinistri` FOREIGN KEY (`IDSinistro`) REFERENCES `sinistri` (`IDSinistro`) ON DELETE CASCADE;

--
-- Limiti per la tabella `sinistri_foto`
--
ALTER TABLE `sinistri_foto`
  ADD CONSTRAINT `fk_sinistri_foto_sinistri` FOREIGN KEY (`IDSinistro`) REFERENCES `sinistri` (`IDSinistro`) ON DELETE CASCADE;

--
-- Limiti per la tabella `utenti`
--
ALTER TABLE `utenti`
  ADD CONSTRAINT `utenti_ibfk_1` FOREIGN KEY (`IDRuolo`) REFERENCES `ruoli` (`IDRuolo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
