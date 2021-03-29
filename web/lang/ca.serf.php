<?php
// plug/lang/*.menu.php

//Serf

//alerts
addS("serf_alert_installed", "Serf està instal·lat");
addS("serf_alert_not_installed", "Serf no està instal·lat");
addS("serf_alert_not_publishing", "Serf no està publicant els serveis locals");
addS("serf_alert_publishing", "Serf està publicant els serveis locals");
addS("serf_alert_running", "Serf està en marxa");
addS("serf_alert_stopped", "Serf està aturat");
addS("serf_alert_will_publish", "Serf publicarà els serveis locals");
addS("serf_alert_wont_publish", "Serf no publicarà els serveis locals");

//buttons
addS("serf_button_install", "Instal·la Serf");
addS("serf_button_publish", "Activa la publicació de serveis via Serf");
addS("serf_button_unpublish", "Desactiva la publicació de serveis via Serf");
addS("serf_button_save", "Desa la configuració");
addS("serf_button_start", "Arrenca Serf");
addS("serf_button_stop", "Atura Serf");
addS("serf_button_uninstall", "Desinstal·la Serf");

//common
addS("serf_common_title", "Serf");
addS("serf_common_subtitle", "Una eina d'Anunci i Descoberta de Serveis Distribuïda (DADS)");

//flash
addS("serf_flash_installed", "S'ha instal·lat Serf");
addS("serf_flash_publishing", "S'està activant la publicació de serveis mitjançant Serf...");
addS("serf_flash_publishing_error", "No s'ha pogut activar la publicació de serveis mitjançant Serf");
addS("serf_flash_saving", "S'està desant la configuració de Serf...");
addS("serf_flash_starting", "S'està arrencant Serf...");
addS("serf_flash_stopping", "S'està aturant Serf...");
addS("serf_flash_uninstalled", "S'ha desinstal·lat Serf");
addS("serf_flash_unpublishing", "S'està desactivant la publicació de serveis via Serf...");
addS("serf_flash_unpublishing_error", "No s'ha pogut desactivar la publicació de serveis mitjançant Serf");

//index
addS("serf_index_change_configuration", "Feu servir aquest formulari per modificar la configuració de Serf:");
addS("serf_index_current_configuration", "Aquesta és la configuració actual de Serf. Per canviar-la, primer atureu Serf.");
addS("serf_index_description_1", "<a href='https://serfdom.io/' target='_blank'>Serf</a> és una solució descentralitzada, lleugera i d'alta disponibilitat per gestionar la pertanyença a clústers, la detecció de fallades i l'orquestració de serveis. Utilitza un protocol eficient i lleuger de <i>gossip</i> (xafardeig) per a la comunicació entre nodes. Com que Serf no depèn de nodes mestres (<i>master</i>) no té punts únics de fallada.");
addS("serf_index_description_2", "El sistema d'Anunci i Descoberta de Serveis Distribuït (en anglès, <i>DADS</i>) inclòs a Cloudy utilitza Serf per intercanviar informació entre nodes. Si la publicació de serveis està activada, els serveis locals seran anunciats a la xarxa i altres usuaris podran veure'ls i usar-los.");
addS("serf_index_form_bootstrap", "Node d'arrencada");
addS("serf_index_form_bootstrap_tooltip", "L'adreça IP i el port d'un node que estigui executant Serf. S'utilitzarà per arrencar el servei. Valor per defecte: 10.139.40.22:5000");
addS("serf_index_form_rpc", "Adreça RPC");
addS("serf_index_form_rpc_tooltip", "L'adreça IP i el port per les crides a procediment remot (en anglès, <i>RPC</i>. Valor per defecte: 127.0.0.1:7373");
addS("serf_index_form_bind", "Port d'escolta");
addS("serf_index_form_bind_tooltip", "El port pel qual Serf escoltarà els missatges que rebi d'altres nodes. Valor per defecte: 5000");
addS("serf_index_subtitle_configuration", "Configuració");
addS("serf_index_publication", "Publicació de serveis:");
addS("serf_index_status", "Estat de Serf:");

//search
addS("serf_search_quality", "Escaneja la qualitat dels serveis");
