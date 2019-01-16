TP Display : 
================= 

Les fichiers customers.csv et purchases.csv sont placés dans data/csv du projet


Install
=======

    $ git clone git@github.com:ibou/display.git 
    or 
    $ git clone https://github.com/ibou/display.git 

    $ cd display
    $ composer install  
    $ php bin/console app:import:data
 
Suggestions
===========
 * Logger les réponses des appels du web service
 * Mettre en place des surveillance dans les cas ou les réponses sont KO
 * Mettre https://api.display-interactive.com dans services.yaml et le récupérer avec $this->container->getParameter();
 * Maximum de test unit, =~100% de couverture 
