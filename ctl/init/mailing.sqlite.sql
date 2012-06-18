
CREATE TABLE emails (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, sujet, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE news (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, sujet, news, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE news_modele (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, nom, modele, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE donnees_email (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_email INT, html, pj, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE expediteurs (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, nom, email, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE envois (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, sujet, html, log, expediteur, nb INT, statut INT, pid INT, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE boite_envoi (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_casquette INT, id_envoi INT, i INT, erreurs INT, date DEFAULT (CURRENT_TIMESTAMP) );

