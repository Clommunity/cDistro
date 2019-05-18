<?php

//alerts
addS("docker_addsources_available", "Docker está disponible para su instalación");
addS("docker_addsources_not_available", "Docker no está disponible para su instalación");
addS("docker_alert_installed", "Docker está instalado");
addS("docker_alert_not_installed", "Docker no está instalado");
addS("docker_alert_not_running", "Docker está parado");
addS("docker_alert_img_not_available", "No hay imágenes de Docker disponibles");
addS("docker_alert_no_sources", "Falta el fichero de fuentes APT para el paquete de Docker");
addS("docker_alert_no_template_pre", "No se ha encontrado la imagen de Docker ");
addS("docker_alert_no_template_post", " en el repositorio local");
addS("docker_alert_no_predef_arch_pre", "No hay contenedores Docker predefinidos para la arquitectura ");
addS("docker_alert_no_predef_arch_post", "");
addS("docker_alert_no_template_pre", "No se ha encontrado la plantilla de Docker ");
addS("docker_alert_no_template_post", "");
addS("docker_alert_ps_not_running", "No hay contenedores Docker en ejecución");
addS("docker_alert_ps_not_stopped", "No hay contenedores Docker parados");
addS("docker_alert_running", "Docker está ejecutándose");
addS("docker_alert_start_message", "Docker se ha iniciado");
addS("docker_alert_stop_message", "Docker se ha parado");
addS("docker_alert_vol_none", "No hay volúmenes Docker disponibles");


//buttons
addS("docker_button_addpd", "Imágenes Docker predefinidas");
addS("docker_button_add_sources", "Añadir el fichero de fuentes APT para Docker");
addS("docker_button_add_sources_retry", "Reintentar añadir el fichero de fuentes APT para Docker");
addS("docker_button_back", "Volver");
addS("docker_button_install", "Instalar Docker (docker.io)");
addS("docker_button_restart", "Reiniciar Docker");
addS("docker_button_search", "Buscar imágenes de Docker");
addS("docker_button_start", "Iniciar Docker");
addS("docker_button_stop", "Parar Docker");
addS("docker_button_uninstall", "Desinstalar Docker (docker.io)");
addS("docker_button_container_publish", "Publicar");
addS("docker_button_container_pull", "Obtener imagen");
addS("docker_button_container_stop", "Parar");
addS("docker_button_container_rm", "Borrar contenedor");
addS("docker_button_container_restart", "Reiniciar contenedor");
addS("docker_button_container_unpublish", "Despublicar");
addS("docker_button_image_confrun", "Configurar e iniciar");
addS("docker_button_image_rmi", "Borrar");
addS("docker_button_image_conf", "Iniciar imagen");
addS("docker_button_pdcontainer_config", "Configurar");
addS("docker_button_pdcontainer_run", "Iniciar");
addS("docker_button_pdform_run", "Iniciar");
addS("docker_button_volume_inspect", "Inspeccionar");
addS("docker_button_volume_rm", "Eliminar");


//flash
addS("docker_flash_restart_pre", "Reiniciando contenedor ");
addS("docker_flash_restart_mid", " (ID: ");
addS("docker_flash_restart_post", ") en segundo plano. Puede tardar unos segundos...");
addS("docker_flash_rm_pre", "Borrando contenedor ");
addS("docker_flash_rm_mid", " (ID: ");
addS("docker_flash_rm_post", ") en segundo plano. Puede tardar unos segundos...");
addS("docker_flash_rmi_pre", "Borrando imagen ");
addS("docker_flash_rmi_mid", "");
addS("docker_flash_rmi_post", " en segundo plano. Puede tardar unos segundos");
addS("docker_flash_run_pre", "Iniciando imagen ");
addS("docker_flash_run_mid", "");
addS("docker_flash_run_post", " en segundo plano. Puede tardar unos segundos");
addS("docker_flash_publish_error_pre", "No se ha podido publicar el contenedor ");
addS("docker_flash_publish_error_post", "");
addS("docker_flash_publish_pre", "Publicando el contenedor ");
addS("docker_flash_publish_post", "");
addS("docker_flash_pull_pre", "Obteniendo imagen ");
addS("docker_flash_pull_post", " en segundo plano. Puede tardar unos segundos");
addS("docker_flash_stop_pre", "Parando contenedor ");
addS("docker_flash_stop_mid", " (ID: ");
addS("docker_flash_stop_post", ") en segundo plano. Puede tardar unos segundos");
addS("docker_flash_unpublish_error_pre", "No se ha podido despublicar el contenedor Docker ");
addS("docker_flash_unpublish_error_post", "");
addS("docker_flash_unpublish_pre", "Se está despublicando el contenedor ");
addS("docker_flash_unpublish_post", "");
addS("docker_flash_vol_rm_pre", "Se está eliminando el volumen ");
addS("docker_flash_vol_rm_post", " en segundo plano. Esto puede tardar unos segundos...");


//Common
addS("docker_title", "Docker");
addS("docker_desc", "Docker es un proyecto de código abierto que automatiza el despliegue de aplicaciones dentro de contenedores software, proporcionando una capa adicional de abstracción y automatización de virtualización a nivel de sistema operativo.");
addS("docker_status", "Estado de Docker:");
addS("docker_subtitle", "Despliegue automatizado de aplicaciones Linux en contenedores software");
addS("docker_container_stopped", "parado");
addS("docker_container_running", "en ejecución");


//add
addS("docker_add_config_desc", "Use el formulario que hay a continuación para configurar las opciones por defecto de la plantilla e iniciar la imagen.");
addS("docker_add_desc", 'La tabla que hay a continuación le ofrece una lista de imágenes Docker predefinidas, probadas y listas para iniciar. Use los botones correspondientes para configurarlas antes de iniciarlas, o para arrancarlas directamente.');
addS("docker_add_error_no_image", 'Ha ocurrido un error:');
addS("docker_add_error_no_template", 'Ha ocurrido un error:');
addS("docker_add_header_appname", 'Aplicación');
addS("docker_add_header_description", 'Descripción');
addS("docker_add_header_ports", 'Puertos');
addS("docker_add_header_options", 'Opciones');
addS("docker_add_header_links", 'Enlaces');
addS("docker_add_header_actions", 'Acciones');
addS("docker_add_pdform_image", "Imagen Docker");
addS("docker_add_pdform_image_tooltip", "La imagen que se descargará del repositorio de Docker.");
addS("docker_add_pdform_name", "Nombre del contenedor");
addS("docker_add_pdform_name_tooltip", "Escriba el nombre para este contenedor Docker, o déjelo en blanco para que reciba un nombre al azar.");
addS("docker_add_pdform_port", "Abrir puerto ");
addS("docker_add_pdform_port_tooltip_pre", "Abrir el puerto TCP ");
addS("docker_add_pdform_port_tooltip_post", " del contendor a través del puerto del anfitrión especificado.");
addS("docker_add_pdform_option", "");
addS("docker_add_pdform_option_tooltip_pre", "Configure el valor para la opción ");
addS("docker_add_pdform_option_tooltip_post", " .");
addS("docker_add_pdform_link", "Enlace");
addS("docker_add_pdform_link_tooltip", "Enlazar este contenedor con otro.");
addS("docker_add_subtitle", "Plantillas predefinidas para iniciar contenedores Docker");
addS("docker_add_subtitle_predefined", "Plantillas de contenedores Docker predefinidas");
addS("docker_add_subtitle_user", "Plantillas de contenedores Docker del usuario");

//search
addS("docker_search_subtitle", "Buscar e instalar imágenes de contenedores Docker");
addS("docker_search_desc", 'Use el formulario a continuación para buscar imágenes de contenedores Docker disponibles en <a href="https://hub.docker.com/">Docker Hub</a> y desplegarlos. La búsqueda se realiza directamente en Docker Hub.');
addS("docker_search_form_automated", 'Automatizado');
addS("docker_search_form_automated_tooltip", 'Buscar sólo imágenes Docker con <a href="https://docs.docker.com/docker-cloud/builds/automated-build/">compilación automática</a>.');
addS("docker_search_form_search", 'Texto de búsqueda');
addS("docker_search_form_search_tooltip", 'Escriba el nombre (o parte) de una imagen de contenedor Docker, un repositorio o una aplicación para buscarlo. Use sólo letras minúsculas y números.');
addS("docker_search_header_name", 'Nombre');
addS("docker_search_header_description", 'Descripción');
addS("docker_search_header_stars", 'Puntuación');
addS("docker_search_header_official", 'Oficial');
addS("docker_search_header_automated", 'Automatizado');
addS("docker_search_header_action", 'Acción');
addS("docker_search_yes", 'Sí');


//main
addS("docker_info", "Información de Docker:");
addS("docker_interface", "Interfaz de red de Docker:");
addS("docker_title_containers_running", "Contenedores Docker en ejecución:");
addS("docker_title_containers_stopped", "Contenedores Docker parados:");
addS("docker_title_images", "Imágenes Docker disponibles:");
addS("docker_title_volume", "Volúmenes Docker disponibles:");
addS("docker_not_installed", "Docker no está instalado.");
addS("docker_installed", "Docker está instalado.");
addS("docker_button_install", "Instalar Docker (Community Edition)");
addS("docker_not_running", "Docker está parado.");
addS("docker_start", "Iniciar Docker.");
addS("docker_remove", "Eliminar Docker.");
addS("docker_running", "Docker está en ejecución.");
addS("docker_sources_manual", 'Falta el fichero de fuentes APT para el paquete de Docker. Haga clic en el botón a continuación para añadirlo automáticamente o siga las instrucciones de <a href="https://docs.docker.com/engine/installation/linux/docker.io/debian/">https://docs.docker.com/engine/installation/linux/docker.io/debian/</a> para hacerlo manualmente');
addS("docker_stop", "Parar Docker.");
addS("docker_start_message", "Docker se ha iniciado.");
addS("docker_stop_message", "Docker se ha parado.");


//addsources
addS("docker_addsources_update", "Actualizando la lista de paquetes...");
addS("docker_addsources_install_https", "Instalando el soporte para transporte HTTPS de APT y otros paquetes requeridos...");
addS("docker_addsources_dockerlist_pre", "Añadiendo el repositorio de paquetes de Docker en ");
addS("docker_addsources_dockerlist_post", ":");
addS("docker_addsources_aptkey", "Importando la clave GPG oficial de Docker al llavero local:");
addS("docker_addsources_update_again", "Actualizando la lista de paquetes de nuevo...");
addS("docker_addsources_result", "Resultado del proceso:");
