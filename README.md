iumm
====

Logiciel de gestion de contacts

http://iumm.surlefil.org/demo

Identifiants :

- lecture seule : demo1 / demo
- lecture écriture : demo2 / demo
- lecture ecriture + mailing (désactivé pour la demo) : demo3 / demo
- acces administrateur : admin / admin

##Prérequis
###Serveur
* Un serveur web (testé avec apache2)
* PHP5 avec support SQLite3 et curl
* Le serveur doit résoudre correctement son propre nom de domaine.

Si vous accédez à iumm avec cette adresse www.example.org/iumm alors il faut que pour le serveur, www.example.org pointe vers localhost. Si ce n'est pas le cas, il faut modifier le fichier /etc/hosts et rajouter la ligne :

    127.0.0.1       www.example.org

###Client
Iumm n'a été testé qu'avec Firefox 3.6+ et Chrome... Iumm ne fonctionne pas avec IE7 ni IE8.

##Installation
Les repertoires suivants doivent être accessibles en écriture par le serveur web :

    db/
    ui/cache/db/
    fichiers/emails/
    fichiers/envois/
    tmp/
    modele/corbeille/

Exemple d'installation sous ubuntu :

    sudo aptitude install apache2 php5-sqlite php5-curl unzip
    sudo /etc/init.d/apache2 restart
    cd /var/www/
    sudo wget https://github.com/FabriceLapeyrere/iumm/zipball/master
    sudo unzip master
    sudo mv FabriceLapeyrere-iumm-xxxxxxx iumm
    sudo chown www-data:www-data -R iumm
    cd iumm
    sudo chmod u+w db/ ui/cache/db/ fichiers/emails/ fichiers/envois/ tmp/ modele/corbeille/
    cd ..
    rm master

Iumm est maintenant disponible à l'url http://localhost/iumm/
