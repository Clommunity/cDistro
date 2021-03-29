<?php
// plug/lang/*.menu.php

//Serf

//alerts
addS("serf_alert_installed", "Serf está instalado");
addS("serf_alert_not_installed", "Serf no está instalado");
addS("serf_alert_not_publishing", "Serf no está publicando los servicios locales");
addS("serf_alert_publishing", "Serf está publicando los servicios locales");
addS("serf_alert_running", "Serf está ejecutándose");
addS("serf_alert_stopped", "Serf está parado");
addS("serf_alert_will_publish", "Serf publicará los servicios locales");
addS("serf_alert_wont_publish", "Serf no publicará los servicios locales");

//buttons
addS("serf_button_install", "Instalar Serf");
addS("serf_button_publish", "Activar la publicación de servicios vía Serf");
addS("serf_button_unpublish", "Desactivar la publicación de servicios vía Serf");
addS("serf_button_save", "Guardar la configuración");
addS("serf_button_start", "Iniciar Serf");
addS("serf_button_stop", "Parar Serf");
addS("serf_button_uninstall", "Desinstalar Serf");

//common
addS("serf_common_title", "Serf");
addS("serf_common_subtitle", "Una herramiente de Anuncio y Descubrimiento de Servicios Distribuida (DADS)");

//flash
addS("serf_flash_installed", "Serf ha sido instalado");
addS("serf_flash_publishing", "Activando la publicación de servicios vía Serf...");
addS("serf_flash_publishing_error", "No se ha podido activar la publicación de servicios vía Serf");
addS("serf_flash_saving", "Guardando la configuración de Serf...");
addS("serf_flash_starting", "Iniciando Serf...");
addS("serf_flash_stopping", "Parando Serf...");
addS("serf_flash_uninstalled", "Serf ha sido desinstalado");
addS("serf_flash_unpublishing", "Desactivando la publicación de servicios vía Serf...");
addS("serf_flash_unpublishing_error", "No se ha podido desactivar la publicación de servicios vía Serf");

//index
addS("serf_index_change_configuration", "Utilice este formulario para modificar la configuración de Serf:");
addS("serf_index_current_configuration", "Esta es la configuración actual de Serf. Para cambiarla, pare Serf primero.");
addS("serf_index_description_1", "<a href='https://serfdom.io/' target='_blank'>Serf</a> es una solución descentralizada, ligera y de alta disponibilidad para gestionar la pertenencia a clústeres, la detección de fallos y la orquestación de servicios. Utiliza un protocolo eficiente y ligero de <i>gossip</i> (cotilleo) para la comunicación entre nodos. Como Serf no depende de nodes maestros (<i>master</i>) no tiene puntos únicos de fallo.");
addS("serf_index_description_2", "El sistema de Anuncio y Descubrimiento de Servicios Distribuido (en inglés, <i>DADS</i>) incluido en Cloudy utiliza Serf para intercambiar información entre nodos. Si la publicación de servicios está activada, los servicios locales serán anunciados a la red y otros usuarios podrán verlos y usarlos.");
addS("serf_index_form_bootstrap", "Nodo de arranque");
addS("serf_index_form_bootstrap_tooltip", "Dirección IP y puerto de un node que esté corriendo Serf. Se utilizará para arrancar el servicio. Valor por defecto: 10.139.40.22:5000");
addS("serf_index_form_rpc", "Adreça RPC");
addS("serf_index_form_rpc_tooltip", "Dirección IP y puerto para las llamadas a procedimiento remoto (en inglés, <i>RPC</i>. Valor por defecto: 127.0.0.1:7373");
addS("serf_index_form_bind", "Puerto de escucha");
addS("serf_index_form_bind_tooltip", "El puerto por el que Serf escuchará los mensajes que reciba de otros nodos. Valor por defecto: 5000");
addS("serf_index_subtitle_configuration", "Configuración");
addS("serf_index_publication", "Publicación de servicios:");
addS("serf_index_status", "Estado de Serf:");

//search
addS("serf_search_quality", "Escanear la calidad de los servicios");
